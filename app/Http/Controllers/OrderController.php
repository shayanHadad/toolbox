<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const STATUSES = [
        Order::STATUS_WAITING,
        Order::STATUS_IN_PROGRESS,
        Order::STATUS_FINISHED,
        Order::STATUS_REJECTED,
        Order::STATUS_CANCELLED,
    ];

    /**
     * ثبت سفارش یک مشتری برای یک متخصص.
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری) مجاز است؛
     * این محدودیت روی روت با میدلور role:1 اعمال شده.
     */
    public function storeForExpert(Request $request, User $expert)
    {
        abort_unless($expert->role == 2 && $expert->expertDetail, 404);

        $user = $request->user();

        $data = $request->validate([
            'details'        => ['required', 'string', 'max:2000'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'details.required'        => 'لطفاً توضیح بده چه خدماتی نیاز داری.',
            'details.max'             => 'توضیحات نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
            'preferred_date.required' => 'لطفاً تاریخ مورد نظرت رو انتخاب کن.',
            'preferred_date.date'     => 'تاریخ وارد شده معتبر نیست.',
            'preferred_date.after_or_equal' => 'تاریخ مورد نظر نمی‌تونه قبل از امروز باشه.',
        ]);

        Order::create([
            'customerID' => $user->userID,
            'providerID' => $expert->userID,
            'companyID'  => null,
            'status'     => Order::STATUS_WAITING,
            'details'    => $data['details'],
            'order_date' => $data['preferred_date'],
        ]);

        return back()->with('success', 'سفارشت با موفقیت برای این متخصص ثبت شد.');
    }

    /**
     * ثبت سفارش یک مشتری برای یک شرکت.
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری) مجاز است؛
     * این محدودیت روی روت با میدلور role:1 اعمال شده.
     */
    public function storeForCompany(Request $request, Company $company)
    {
        $user = $request->user();

        $data = $request->validate([
            'details'        => ['required', 'string', 'max:2000'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'details.required'        => 'لطفاً توضیح بده چه خدماتی نیاز داری.',
            'details.max'             => 'توضیحات نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
            'preferred_date.required' => 'لطفاً تاریخ مورد نظرت رو انتخاب کن.',
            'preferred_date.date'     => 'تاریخ وارد شده معتبر نیست.',
            'preferred_date.after_or_equal' => 'تاریخ مورد نظر نمی‌تونه قبل از امروز باشه.',
        ]);

        Order::create([
            'customerID' => $user->userID,
            'providerID' => null,
            'companyID'  => $company->companyID,
            'status'     => Order::STATUS_WAITING,
            'details'    => $data['details'],
            'order_date' => $data['preferred_date'],
        ]);

        return back()->with('success', 'سفارشت با موفقیت برای این شرکت ثبت شد.');
    }

    /**
     * تاریخچه‌ی کامل سفارش‌ها؛ بسته به رول کاربر یه چیز متفاوت نشون می‌ده:
     * - مشتری (role=1): سفارش‌هایی که به‌عنوان مشتری ثبت کرده.
     * - متخصص (role=2): سفارش‌هایی که به‌عنوان ارائه‌دهنده دریافت کرده.
     * - نماینده‌ی شرکت (role=3، role=4): سفارش‌های شرکتی که توش عضو ادمینه.
     *
     * قابل فیلتر بر اساس وضعیت (status) و قابل مرتب‌سازی (sort):
     * جدیدترین، قدیمی‌ترین، یا بر اساس وضعیت.
     */
    public function index(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();

        $status = $request->query('status');
        $status = is_numeric($status) ? (int) $status : null;
        $sort   = $request->query('sort', 'newest');

        if (! in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        $query = match (true) {
            (int) $user->role === 1 => $user->customerOrders()->with(['provider', 'company']),
            (int) $user->role === 2 => $user->providerOrders()->with('customer'),
            in_array((int) $user->role, [3, 4], true) => $this->companyOf($user)?->ordersVisibleTo($user)->with('customer'),
            default => null,
        };

        $orders = collect();

        if ($query) {
            if ($status) {
                $query->where('status', $status);
            }

            // سفارش‌های در انتظار تأیید همیشه بالاتر از بقیه نشون داده بشن
            $query->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Order::STATUS_WAITING]);

            match ($sort) {
                'oldest' => $query->orderBy('orderID'),
                // چون کدهای عددی وضعیت (1..5) از قبل دقیقاً با همین اولویت
                // (در انتظار → در حال انجام → تمام‌شده → رد شده → لغو شده) تعریف شدن،
                // مرتب‌سازی مستقیم روی ستون status کافیه و دیگه نیازی به CASE نیست.
                'status' => $query->orderBy('status'),
                default => $query->orderByDesc('orderID'),
            };

            $orders = $query->get();
        }

        return view('orders.index', [
            'user'   => $user,
            'orders' => $orders,
            'status' => $status,
            'sort'   => $sort,
        ]);
    }

    /**
     * درخواست‌های سفارشِ در انتظار تأیید، مخصوص متخصص (role=2) و
     * نماینده‌ی شرکت (role=3) و مالک شرکت (role=4)، تا بتونن تأیید یا ردشون کنن.
     */
    public function requests(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();

        $orders = match (true) {
            (int) $user->role === 2 => $user->providerOrders()->where('status', Order::STATUS_WAITING)->with('customer')->orderByDesc('orderID')->get(),
            in_array((int) $user->role, [3, 4], true) => $this->companyOf($user)?->ordersVisibleTo($user)->where('status', Order::STATUS_WAITING)->with('customer')->orderByDesc('orderID')->get() ?? collect(),
            default => collect(),
        };

        return view('orders.requests', [
            'user'   => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * تأیید یک سفارشِ در انتظار (وضعیتش می‌ره روی «در حال انجام»).
     */
    public function approve(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($request->user(), $order);

        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_IN_PROGRESS]);

        return back()->with('success', 'سفارش تأیید شد و به مرحله‌ی «در حال انجام» رفت.');
    }

    /**
     * رد کردن یک سفارشِ در انتظار (توسط متخصص/شرکت).
     */
    public function reject(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($request->user(), $order);

        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_REJECTED]);

        return back()->with('success', 'سفارش رد شد و به مشتری اطلاع داده می‌شه.');
    }

    /**
     * لغو یک سفارش توسط خودِ مشتری؛ فقط تا وقتی سفارش هنوز تأیید نشده
     * (status = waiting) امکان‌پذیره.
     */
    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($order->customerID === $user->userID, 403);
        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return back()->with('success', 'سفارش با موفقیت لغو شد.');
    }

    /**
     * ثبت نظر و امتیاز مشتری برای یک سفارشِ تمام‌شده.
     */
    public function review(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($order->customerID === $user->userID, 403);
        abort_unless($order->status === Order::STATUS_FINISHED, 404);
        abort_if(! $order->needsReview(), 409, 'برای این سفارش قبلاً نظر ثبت شده.');

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ], [
            'rating.required'  => 'لطفاً یک امتیاز بین ۱ تا ۵ انتخاب کن.',
            'rating.between'   => 'امتیاز باید بین ۱ تا ۵ باشه.',
            'comment.required' => 'لطفاً نظرت رو بنویس.',
            'comment.max'      => 'نظر نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
        ]);

        $order->update([
            'rating'  => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return back()->with('success', 'نظر و امتیازت با موفقیت ثبت شد. ممنون بابت وقتی که گذاشتی 🙏');
    }

    /**
     * مطمئن می‌شه کاربرِ فعلی واقعاً صاحبِ این سفارش (به‌عنوان متخصص یا
     * نماینده‌ی شرکتِ مربوطه) هست، وگرنه 403 برمی‌گردونه.
     */
    private function authorizeOrderOwner(User $user, Order $order): void
    {
        if ((int) $user->role === 2) {
            abort_unless($order->providerID === $user->userID, 403);

            return;
        }

        if (in_array((int) $user->role, [3, 4], true)) {
            $company = $this->companyOf($user);
            abort_unless($company && $order->companyID === $company->companyID, 403);

            return;
        }

        abort(403);
    }

    /**
     * شرکتی که کاربرِ نماینده (role=3، ادمین شرکت) یا مالک شرکت (role=4)
     * عضو ادمینشه؛ اگه به هیچ شرکتی متصل نباشه null برمی‌گرده.
     */
    private function companyOf(User $user): ?Company
    {
        return $user->companyAdmin?->company;
    }
}
