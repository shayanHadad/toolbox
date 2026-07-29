<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * لیست مکالمه‌های کاربر لاگین‌کرده.
     * - مشتری/متخصص (role=1,2): مثل قبل، به‌علاوه اینکه اگر با یک شرکت
     *   چت کرده باشند، تمام پیام‌های آن شرکت (مستقل از اینکه کدوم ادمین
     *   پاسخ داده) در یک ردیف واحد نمایش داده می‌شود.
     * - ادمین/مالک شرکت (role=3,4): لیست مشتری‌هایی که با شرکت (نه با
     *   خودِ این ادمین) چت کرده‌اند؛ تاریخچه‌ی مشترک بین همه‌ی ادمین‌ها.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (in_array((int) $user->role, [3, 4], true)) {
            return $this->indexForCompany($user);
        }

        return $this->indexForRegularUser($user);
    }

    protected function indexForRegularUser(User $user)
    {
        $messages = Message::where('senderID', $user->userID)
            ->orWhere('receiverID', $user->userID)
            ->get(['senderID', 'receiverID', 'companyID']);

        // مکالمات شخصی (بدون شرکت): دقیقا مثل رفتار قبلی، گروه‌بندی بر اساس طرف مقابل
        $personalPartnerIds = $messages
            ->whereNull('companyID')
            ->map(fn($m) => $m->senderID == $user->userID ? $m->receiverID : $m->senderID)
            ->unique();

        $personalConversations = User::withTrashed()->whereIn('userID', $personalPartnerIds)
            ->get()
            ->map(function (User $partner) use ($user) {
                $lastMessage = $this->conversationQuery($user, $partner)
                    ->latest('messageID')
                    ->first();

                $unreadCount = Message::where('senderID', $partner->userID)
                    ->where('receiverID', $user->userID)
                    ->whereNull('companyID')
                    ->where('status', 0)
                    ->count();

                return (object) [
                    'type'        => 'user',
                    'partner'     => $partner,
                    'company'     => null,
                    'lastMessage' => $lastMessage,
                    'unreadCount' => $unreadCount,
                ];
            });

        // مکالمات شرکتی: یک ردیف واحد به‌ازای هر companyID، نه یکی برای هر ادمین
        $companyIds = $messages->pluck('companyID')->filter()->unique();

        $companyConversations = $companyIds->map(function ($companyID) use ($user) {
            $lastMessage = Message::forCompany($companyID)
                ->where(function ($q) use ($user) {
                    $q->where('senderID', $user->userID)
                        ->orWhere('receiverID', $user->userID);
                })
                ->latest('messageID')
                ->first();

            $unreadCount = Message::forCompany($companyID)
                ->where('receiverID', $user->userID)
                ->where('status', 0)
                ->count();

            // برای لینک به messages.show باید یک User مشخص بدیم (route model
            // binding قبلی حفظ می‌شه)؛ آخرین طرف مقابل کافیه، چون show()
            // تشخیص مکالمه‌ی شرکتی رو از روی companyID انجام می‌ده، نه از
            // روی اینکه دقیقاً کدوم ادمین partner هست.
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

        $conversations = $personalConversations->merge($companyConversations)
            ->sort(function ($a, $b) {
                // ابتدا مکالمه‌هایی که پیام خوانده‌نشده دارند
                if (($a->unreadCount > 0) !== ($b->unreadCount > 0)) {
                    return $a->unreadCount > 0 ? -1 : 1;
                }

                // سپس جدیدترین پیام
                return ($b->lastMessage?->messageID ?? 0)
                    <=> ($a->lastMessage?->messageID ?? 0);
            })
            ->values();

        return view('messages.index', [
            'conversations' => $conversations,
        ]);
    }

    protected function indexForCompany(User $user)
    {
        $companyID = Message::companyIdForUser($user);

        abort_unless($companyID, 403, 'کاربر شرکت به هیچ شرکتی متصل نیست.');

        $company = Company::find($companyID);

        abort_unless($company, 404);

        // آی‌دیِ همه‌ی کسانی که تا الان نماینده‌ی این شرکت بوده‌ن، از جمله
        // اون‌هایی که بعداً soft-delete شدن؛ اگه به‌جاش برای هر پیام یه
        // User::find() تازه می‌زدیم، پیامِ فرستاده‌شده توسط یه ادمینِ
        // حذف‌شده اشتباهی «پیامِ مشتری» تشخیص داده می‌شد.
        $repIds = $company->repUserIds();

        $companyMessages = Message::forCompany($companyID)->get(['senderID', 'receiverID']);

        // مشتری همیشه طرفی از پیام است که عضو شرکت (role 3/4) نیست
        $customerIds = $companyMessages
            ->map(fn (Message $m) => in_array($m->senderID, $repIds, true) ? $m->receiverID : $m->senderID)
            ->unique();

        $conversations = User::withTrashed()->whereIn('userID', $customerIds)
            ->get()
            ->map(function (User $customer) use ($companyID) {
                $lastMessage = Message::forCompany($companyID)
                    ->where(function ($q) use ($customer) {
                        $q->where('senderID', $customer->userID)
                            ->orWhere('receiverID', $customer->userID);
                    })
                    ->latest('messageID')
                    ->first();

                // خوانده‌نشده = پیام‌هایی که این مشتری فرستاده و هنوز توسط
                // هیچ‌کدام از اعضای شرکت خوانده نشده (اینباکس مشترک)
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
            // ادمین شرکت (role=3) فقط مکالمه‌هایی رو می‌بینه که فعلاً پیام
            // خوانده‌نشده دارن؛ مالک شرکت (role=4) به همه‌شون دسترسی داره.
            ->when((int) $user->role === 3, fn ($conversations) => $conversations->filter(
                fn ($conversation) => $conversation->unreadCount > 0
            ))
            ->sort(function ($a, $b) {
                if (($a->unreadCount > 0) !== ($b->unreadCount > 0)) {
                    return $a->unreadCount > 0 ? -1 : 1;
                }

                return ($b->lastMessage?->messageID ?? 0)
                    <=> ($a->lastMessage?->messageID ?? 0);
            })
            ->values();

        return view('messages.index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * صفحه‌ی چت.
     * - اگر یکی از دو طرف (کاربر لاگین‌کرده یا partner) عضو شرکت باشد،
     *   کل تاریخچه‌ی companyID <-> مشتری نشون داده می‌شه (مستقل از اینکه
     *   partner دقیقاً کدوم ادمین/مالک شرکت هست).
     * - در غیر این صورت، رفتار قبلی (چت شخصی صرفاً بین این دو نفر) حفظ می‌شه.
     *
     * برخلاف بقیه‌ی مسیرهایی که User رو implicit bind می‌کنن، اینجا
     * partner رو دستی و با withTrashed() پیدا می‌کنیم؛ چون این صفحه
     * قراره تاریخچه‌ی یک مکالمه‌ی *قدیمی* رو نشون بده، حتی اگه طرفِ
     * مکالمه (مثلاً یک ادمین شرکتِ حذف‌شده) دیگه soft-delete شده باشه.
     * store() هم مشابه همین withTrashed() رو داره تا بشه به یک مکالمه‌ی
     * شرکتی که آخرین طرفِ مسیرشده‌اش حذف شده جواب داد؛ فقط برای چتِ
     * شخصیِ (بدون شرکت) با یک کاربرِ کاملاً حذف‌شده، شروع/ادامه‌ی پیامِ
     * جدید همچنان مسدوده (چون دیگه کسی نیست که بخونتش).
     */
    public function show(Request $request, int $partner)
    {
        $user = $request->user();

        $partner = User::withTrashed()->find($partner);

        abort_unless($partner, 404);

        abort_if($user->userID === $partner->userID, 404);

        $companyID = Message::companyIdForUser($user) ?? Message::companyIdForUser($partner);

        if ($companyID) {
            $isStaffViewer = in_array((int) $user->role, [3, 4], true);
            $customer = $isStaffViewer ? $partner : $user;

            if ($isStaffViewer && (int) $user->role === 3) {
                // ادمین شرکت فقط به مکالمه‌هایی دسترسی داره که فعلاً پیام
                // خوانده‌نشده دارن؛ در غیر این صورت اصلاً اجازه‌ی باز
                // کردنش رو نداره (برخلاف مالک شرکت که به همه دسترسی داره).
                $hasUnread = Message::forCompany($companyID)
                    ->where('senderID', $customer->userID)
                    ->where('status', 0)
                    ->exists();

                abort_unless($hasUnread, 404);
            }

            $messages = Message::forCompany($companyID)
                ->where(function ($q) use ($customer) {
                    $q->where('senderID', $customer->userID)
                        ->orWhere('receiverID', $customer->userID);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('messageID')
                ->get();

            if ($isStaffViewer) {
                // پیام‌های ارسالی مشتری برای کل تیم شرکت خوانده‌شده علامت بزن
                Message::forCompany($companyID)
                    ->where('senderID', $customer->userID)
                    ->where('status', 0)
                    ->update(['status' => 1]);
            } else {
                // پیام‌های ارسالی هر یک از اعضای شرکت که برای این مشتری فرستاده شده
                Message::forCompany($companyID)
                    ->where('receiverID', $user->userID)
                    ->where('status', 0)
                    ->update(['status' => 1]);
            }

            return view('messages.show', [
                'partner'  => $partner,
                'messages' => $messages,
                'company'  => Company::find($companyID),
            ]);
        }

        $messages = $this->conversationQuery($user, $partner)
            ->with(['sender', 'receiver'])
            ->orderBy('messageID')
            ->get();

        // پیام‌های دریافتی از طرف مقابل که هنوز خونده نشدن، الان که صفحه باز شد خونده‌شده علامت بزن
        Message::where('senderID', $partner->userID)
            ->where('receiverID', $user->userID)
            ->where('status', 0)
            ->update(['status' => 1]);

        return view('messages.show', [
            'partner'  => $partner,
            'messages' => $messages,
            'company'  => null,
        ]);
    }

    /**
     * ارسال پیام به یک متخصص/شرکت (توسط مشتری) یا پاسخ به یک مشتری
     * (توسط متخصص، ادمین شرکت یا مالک شرکت)، و ریدایرکت به صفحه‌ی چت مشترک.
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری)، role=2 (متخصص)،
     * role=3 (ادمین شرکت) یا role=4 (مالک شرکت) مجاز است؛ این محدودیت
     * روی روت با میدلور role:1,2,3,4 اعمال شده.
     */
    public function store(Request $request, int $partner)
    {
        $user = $request->user();

        // برخلاف implicit binding معمولی (که کاربر soft-delete‌شده رو در
        // نظر نمی‌گیره)، اینجا دستی با withTrashed() پیدا می‌کنیم؛ چون
        // توی یه مکالمه‌ی شرکتی، ممکنه آخرین طرفِ مسیرشده (routePartnerId)
        // یه ادمین/مالکِ حذف‌شده باشه، درحالی‌که بقیه‌ی اعضای فعالِ همون
        // شرکت هنوز باید بتونن پیامِ جدید رو دریافت کنن.
        $partner = User::withTrashed()->find($partner);

        abort_unless($partner, 404);
        abort_if($user->userID === $partner->userID, 404);

        // اگر فرستنده یا گیرنده عضو شرکت (role 3/4) باشه، پیام به companyID
        // مربوطه وصل می‌شه تا بین همه‌ی ادمین‌های شرکت مشترک باشه؛ در غیر
        // این صورت null می‌مونه (چت شخصی).
        $companyID = Message::resolveCompanyId($user, $partner);

        // اگه این یه مکالمه‌ی شرکتی نیست (یعنی نه فرستنده نه گیرنده عضو
        // شرکتن) و طرفِ مقابل soft-delete شده، اجازه‌ی شروع/ادامه‌ی پیامِ
        // جدید داده نمی‌شه؛ چون دیگه کاربر فعالی نیست که بخونتش. برای
        // مکالمه‌ی شرکتی این محدودیت اعمال نمی‌شه، چون پیام به کل اینباکسِ
        // مشترکِ شرکت می‌ره، نه فقط به همین یک نفر.
        abort_if(! $companyID && $partner->trashed(), 404);

        if ($user->role == 1) {
            // مشتری می‌تونه به متخصص‌های با پروفایل تکمیل‌شده یا نماینده‌ی یه شرکت پیام بده
            $isExpert = $partner->role == 2 && $partner->expertDetail;
            $isCompanyRep = in_array((int) $partner->role, [3, 4], true) && $companyID;

            abort_unless($isExpert || $isCompanyRep, 404);
        } elseif (in_array((int) $user->role, [2, 3, 4], true)) {
            // متخصص، ادمین شرکت یا مالک شرکت فقط می‌تونه به مشتری‌ها پاسخ بده
            abort_unless($partner->role == 1, 404);
        } else {
            abort(403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'متن پیام رو بنویس.',
            'message.max'      => 'پیام نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
        ]);

        Message::create([
            'senderID'   => $user->userID,
            'receiverID' => $partner->userID,
            'message'    => $data['message'],
            'status'     => 0, // unread
            'companyID'  => $companyID,
        ]);

     if ((int) $user->role === 3) {
    return redirect()
        ->route('messages.index')
        ->with('success', 'پیامت با موفقیت ارسال شد.');
}

return redirect()
    ->route('messages.show', $partner->userID)
    ->with('success', 'پیامت با موفقیت ارسال شد.');
    }

    /**
     * تمام پیام‌های رد و بدل شده (در هر دو جهت) بین دو کاربر، فقط برای
     * چت شخصی (بدون شرکت). چت‌های شرکتی در show()/index() با companyID
     * و اسکوپ forCompany() مدیریت می‌شوند.
     */
    private function conversationQuery(User $a, User $b)
    {
        return Message::betweenUsers($a->userID, $b->userID);
    }
}
