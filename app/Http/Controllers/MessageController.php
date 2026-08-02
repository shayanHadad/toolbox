<?php
//--//
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    // Return the list of chats base on roles
    public function index(Request $request)
    {
        $user = $request->user();

        // If user has roles 3, 4
        if (in_array((int) $user->role, [3, 4], true)) {
            return $this->indexForCompany($user);
        }

        // If user has roles 1, 2
        return $this->indexForRegularUser($user);
    }

    // Finding the list of chats for users with roles 1, 2
    protected function indexForRegularUser(User $user)
    {
        $messages = Message::where('senderID', $user->userID)
            ->orWhere('receiverID', $user->userID)
            ->get(['senderID', 'receiverID', 'companyID']);

        // Chats with users with role = 2
        $personalPartnerIds = $messages
            ->whereNull('companyID')
            // Find chats that users was involved in and map the sender and receiver IDs
            ->map(fn($m) => $m->senderID == $user->userID ? $m->receiverID : $m->senderID)
            ->unique();

        // If other side of the chat is soft-delted do not show it
        $personalConversations = User::whereIn('userID', $personalPartnerIds)
            ->get()
            ->map(function (User $partner) use ($user) { // Find the last message between 2 users
                $lastMessage = $this->conversationQuery($user, $partner)
                    ->latest('messageID')
                    ->first();

                // Count the unread messages in this chat
                $unreadCount = Message::where('senderID', $partner->userID)
                    ->where('receiverID', $user->userID)
                    ->whereNull('companyID')
                    ->where('status', 0)
                    ->count();

                // Return the data
                return (object) [
                    'type'        => 'user',
                    'partner'     => $partner,
                    'company'     => null,
                    'lastMessage' => $lastMessage,
                    'unreadCount' => $unreadCount,
                ];
            });

        // Company conversations
        // Get the company IDs that user has a chat with
        $companyIds = $messages->pluck('companyID')->filter()->unique();

        // Fetch the last message of each conversation with each company
        $companyConversations = $companyIds->map(function ($companyID) use ($user) {
            $lastMessage = Message::forCompany($companyID)
                ->where(function ($q) use ($user) {
                    $q->where('senderID', $user->userID)
                        ->orWhere('receiverID', $user->userID);
                })
                ->latest('messageID')
                ->first();

            // Count the unread messages for each chat with each company
            $unreadCount = Message::forCompany($companyID)
                ->where('receiverID', $user->userID)
                ->where('status', 0)
                ->count();

            // To get the chat partner information
            $routePartnerId = $lastMessage
                ? ($lastMessage->senderID == $user->userID ? $lastMessage->receiverID : $lastMessage->senderID)
                : null;

            return (object) [
                'type'        => 'company',
                'partner'     => $routePartnerId ? User::withTrashed()->find($routePartnerId) : null,
                'company'     => Company::find($companyID),
                'lastMessage' => $lastMessage,
                'unreadCount' => $unreadCount,
            ];
        });

        // Merge the both company and expert conversations
        $conversations = $personalConversations->merge($companyConversations)
            ->sort(function ($a, $b) {
                // Sort the messages so unread messages are on top
                if (($a->unreadCount > 0) !== ($b->unreadCount > 0)) {
                    return $a->unreadCount > 0 ? -1 : 1;
                }

                // After unread messages sort based on the newest chats
                return ($b->lastMessage?->messageID ?? 0)
                    <=> ($a->lastMessage?->messageID ?? 0);
            })
            ->values();

        // Return the view with proper datas
        return view('messages.index', [
            'conversations' => $conversations,
        ]);
    }

    // Showing index for companies
    protected function indexForCompany(User $user)
    {
        // Find the company ID
        $companyID = Message::companyIdForUser($user);

        abort_unless($companyID, 403, 'کاربر شرکت به هیچ شرکتی متصل نیست.');

        // Find the company
        $company = Company::find($companyID);

        abort_unless($company, 404);

        // Find all the company admin Ids (even soft deleted ones)
        $repIds = $company->repUserIds();

        // Get all company messages
        $companyMessages = Message::forCompany($companyID)->get(['senderID', 'receiverID']);

        // Find all customer IDs that have chats with the company
        $customerIds = $companyMessages
            ->map(fn(Message $m) => in_array($m->senderID, $repIds, true) ? $m->receiverID : $m->senderID)
            ->unique();

        // Do not show the conversations with soft deleted users
        $conversations = User::whereIn('userID', $customerIds)
            ->get()
            ->map(function (User $customer) use ($companyID) { // Fetch the last messages of each chat
                $lastMessage = Message::forCompany($companyID)
                    ->where(function ($q) use ($customer) {
                        $q->where('senderID', $customer->userID)
                            ->orWhere('receiverID', $customer->userID);
                    })
                    ->latest('messageID')
                    ->first();

                // Count the unread messages for each chat
                $unreadCount = Message::forCompany($companyID)
                    ->where('senderID', $customer->userID)
                    ->where('status', 0)
                    ->count();

                return (object) [
                    'type'        => 'company',
                    'partner'     => $customer,
                    'company'     => null,
                    'lastMessage' => $lastMessage,
                    'unreadCount' => $unreadCount,
                ];
            })

            // Role = 3 only sees chats with unread messages
            // Role = 4 can see all the chats
            ->when((int) $user->role === 3, fn($conversations) => $conversations->filter(
                fn($conversation) => $conversation->unreadCount > 0
            ))
            // Always show the unread messages on top
            ->sort(function ($a, $b) {
                if (($a->unreadCount > 0) !== ($b->unreadCount > 0)) {
                    return $a->unreadCount > 0 ? -1 : 1;
                }

                // Then sort the newst messages on top
                return ($b->lastMessage?->messageID ?? 0)
                    <=> ($a->lastMessage?->messageID ?? 0);
            })
            ->values();

        // Return the index for companies
        return view('messages.index', [
            'conversations' => $conversations,
        ]);
    }

    // Chat page
    public function show(Request $request, int $partner)
    {
        $user = $request->user();

        $partner = User::withTrashed()->find($partner);

        abort_unless($partner, 404);

        // User can not open a chat with a user that got soft-deleted
        abort_if((int) $partner->role === 1 && $partner->trashed(), 404);

        // User ID and partner ID are the same
        abort_if($user->userID === $partner->userID, 404);

        // Find company ID if it user or partner has roles 3, 4
        $companyID = Message::companyIdForUser($user) ?? Message::companyIdForUser($partner);

        if ($companyID) {
            $isStaffViewer = in_array((int) $user->role, [3, 4], true);

            // Making sure one side of the chat has role = 1
            if ($isStaffViewer) {
                abort_unless((int) $partner->role === 1, 404);
            } else {
                abort_unless((int) $user->role === 1 && in_array((int) $partner->role, [3, 4], true), 404);
            }

            // Determain that customer is partner or the main user
            $customer = $isStaffViewer ? $partner : $user;

            // If user has role = 3 only can open chats with unread messages
            if ($isStaffViewer && (int) $user->role === 3) {
                $hasUnread = Message::forCompany($companyID)
                    ->where('senderID', $customer->userID)
                    ->where('status', 0)
                    ->exists();

                abort_unless($hasUnread, 404);
            }

            // Fetch the messages for company
            $messages = Message::forCompany($companyID)
                ->where(function ($q) use ($customer) {
                    $q->where('senderID', $customer->userID)
                        ->orWhere('receiverID', $customer->userID);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('messageID')
                ->get();

            if ($isStaffViewer) {
                // If user is not customer mark all messages as read
                Message::forCompany($companyID)
                    ->where('senderID', $customer->userID)
                    ->where('status', 0)
                    ->update(['status' => 1]);
            } else {
                // If user has role = 1 mark all messages as read
                Message::forCompany($companyID)
                    ->where('receiverID', $user->userID)
                    ->where('status', 0)
                    ->update(['status' => 1]);
            }

            // Return the chat page
            return view('messages.show', [
                'partner'  => $partner,
                'messages' => $messages,
                'company'  => Company::find($companyID),
            ]);
        }


        // Chat partner is an expert
        abort_unless($this->canConverse($user, $partner), 404);

        // Fetch all the conversation messages
        $messages = $this->conversationQuery($user, $partner)
            ->with(['sender', 'receiver'])
            ->orderBy('messageID')
            ->get();

        // Mark messages as read
        Message::where('senderID', $partner->userID)
            ->where('receiverID', $user->userID)
            ->where('status', 0)
            ->update(['status' => 1]);

        // Return the chat page
        return view('messages.show', [
            'partner'  => $partner,
            'messages' => $messages,
            'company'  => null,
        ]);
    }

    // Sending a new message
    public function store(Request $request, int $partner)
    {
        $user = $request->user();

        // Find the partner even if has been soft-deleted
        $partner = User::withTrashed()->find($partner);

        abort_unless($partner, 404);

        // Abort if the expert has been deleted
        abort_if((int) $partner->role === 1 && $partner->trashed(), 404);

        // Abort if user is chatting to itself
        abort_if($user->userID === $partner->userID, 404);

        // Find company ID if one side of the chat is company
        $companyID = Message::resolveCompanyId($user, $partner);

        // Abort if it is not a company and expert is soft-deleted
        abort_if(! $companyID && $partner->trashed(), 404);

        abort_unless($this->canConverse($user, $partner), 404);

        // Customer can chat with expert unless it has details
        if ((int) $user->role === 1 && (int) $partner->role === 2) {
            abort_unless($partner->expertDetail, 404);
        }

        // Validate the datas
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'متن پیام رو بنویس.',
            'message.max'      => 'پیام نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
        ]);

        // Add the message to database
        Message::create([
            'senderID'   => $user->userID,
            'receiverID' => $partner->userID,
            'message'    => $data['message'],
            'status'     => 0, // unread
            'companyID'  => $companyID,
        ]);

        // If user has role = 3 redirect to index page
        if ((int) $user->role === 3) {
            return redirect()
                ->route('messages.index')
                ->with('success', 'پیامت با موفقیت ارسال شد.');
        }

        // If user has roles 1, 2, 4 stay on the page only refresh it
        return redirect()
            ->route('messages.show', $partner->userID)
            ->with('success', 'پیامت با موفقیت ارسال شد.');
    }

    // Fetch a complete conversation between users with roles 1 and 2
    private function conversationQuery(User $a, User $b)
    {
        return Message::betweenUsers($a->userID, $b->userID);
    }

    // Check if two users can talk
    // One side of the chat always has to be a role = 1
    private function canConverse(User $a, User $b): bool
    {
        $roleA = (int) $a->role;
        $roleB = (int) $b->role;

        return ($roleA === 1 && in_array($roleB, [2, 3, 4], true))
            || ($roleB === 1 && in_array($roleA, [2, 3, 4], true));
    }
}
