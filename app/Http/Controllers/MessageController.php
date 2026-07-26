<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * لیست مکالمه‌های کاربر لاگین‌کرده (گروه‌بندی‌شده بر اساس طرف مکالمه)،
     * جدیدترین مکالمه اول.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $partnerIds = Message::where('senderID', $user->userID)
            ->orWhere('receiverID', $user->userID)
            ->get(['senderID', 'receiverID'])
            ->map(fn($m) => $m->senderID == $user->userID ? $m->receiverID : $m->senderID)
            ->unique();

        $conversations = User::whereIn('userID', $partnerIds)
            ->get()
            ->map(function (User $partner) use ($user) {
                $lastMessage = $this->conversationQuery($user, $partner)
                    ->latest('messageID')
                    ->first();

                $unreadCount = Message::where('senderID', $partner->userID)
                    ->where('receiverID', $user->userID)
                    ->where('status', 0)
                    ->count();

                return (object) [
                    'partner'      => $partner,
                    'lastMessage'  => $lastMessage,
                    'unreadCount'  => $unreadCount,
                ];
            })
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

    /**
     * صفحه‌ی چت اختصاصی کاربر لاگین‌کرده با یک نفر دیگه (مثلاً یک متخصص).
     * تاریخچه‌ی کامل پیام‌های رد و بدل شده بین این دو نفر رو نشون می‌ده.
     */
    public function show(Request $request, User $partner)
    {
        $user = $request->user();

        abort_if($user->userID === $partner->userID, 404);

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
        ]);
    }

    /**
     * ارسال پیام به یک متخصص/شرکت (توسط مشتری) یا پاسخ به یک مشتری
     * (توسط متخصص، ادمین شرکت یا مالک شرکت)، و ریدایرکت به صفحه‌ی چت مشترک.
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری)، role=2 (متخصص)،
     * role=3 (ادمین شرکت) یا role=4 (مالک شرکت) مجاز است؛ این محدودیت
     * روی روت با میدلور role:1,2,3,4 اعمال شده.
     */
    public function store(Request $request, User $partner)
    {
        $user = $request->user();

        abort_if($user->userID === $partner->userID, 404);

        if ($user->role == 1) {
            // مشتری می‌تونه به متخصص‌های با پروفایل تکمیل‌شده یا نماینده‌ی یه شرکت پیام بده
            $isExpert = $partner->role == 2 && $partner->expertDetail;
            $isCompanyRep = in_array((int) $partner->role, [3, 4], true) && $partner->companyAdmin?->company;

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
        ]);

        return redirect()
            ->route('messages.show', $partner)
            ->with('success', 'پیامت با موفقیت ارسال شد.');
    }

    /**
     * تمام پیام‌های رد و بدل شده (در هر دو جهت) بین دو کاربر.
     */
    private function conversationQuery(User $a, User $b)
    {
        return Message::where(function ($q) use ($a, $b) {
            $q->where('senderID', $a->userID)->where('receiverID', $b->userID);
        })->orWhere(function ($q) use ($a, $b) {
            $q->where('senderID', $b->userID)->where('receiverID', $a->userID);
        });
    }
}
