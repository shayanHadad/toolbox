<?php
//--//
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

    // Customer (role = 1) requesting an order to an expert (role = 2)
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

    // Store an order request for a company
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

    // Show orders based on user role
    // Filtering and sorting options
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

        // Build the query based on user role
        $query = match (true) {
            (int) $user->role === 1 => $user->customerOrders()->with(['provider', 'company']),
            (int) $user->role === 2 => $user->providerOrders()->with('customer'),
            in_array((int) $user->role, [3, 4], true) => $this->companyOf($user)?->ordersVisibleTo($user)->with('customer'),
            default => null,
        };

        $orders = collect();

        if ($query) {
            // If status was given by user
            if ($status) {
                $query->where('status', $status);
            }

            // Sort the orders if user selected a sorting method
            match ($sort) {
                'oldest' => $query->orderBy('orderID'),
                'status' => $query->orderBy('status'),
                default => $query->orderByDesc('orderID'),
            };

            // Fetch all orders based on filters
            $orders = $query->get();
        }

        // Return the view with data
        return view('orders.index', [
            'user'   => $user,
            'orders' => $orders,
            'status' => $status,
            'sort'   => $sort,
        ]);
    }


    // Order requests for roles 2, 3, 4 
    public function requests(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();

        // Fetch orders based on role
        $orders = match (true) {
            (int) $user->role === 2 => $user->providerOrders()->where('status', Order::STATUS_WAITING)->with('customer')->orderByDesc('orderID')->get(),
            in_array((int) $user->role, [3, 4], true) => $this->companyOf($user)?->ordersVisibleTo($user)->where('status', Order::STATUS_WAITING)->with('customer')->orderByDesc('orderID')->get() ?? collect(),
            default => collect(),
        };

        // Return the proper view
        return view('orders.requests', [
            'user'   => $user,
            'orders' => $orders,
        ]);
    }

    // Approving an order
    public function approve(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($request->user(), $order);

        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_IN_PROGRESS]);

        return back()->with('success', 'سفارش تأیید شد و به مرحله‌ی «در حال انجام» رفت.');
    }

    // Rejecting an order
    public function reject(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($request->user(), $order);

        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_REJECTED]);

        return back()->with('success', 'سفارش رد شد و به مشتری اطلاع داده می‌شه.');
    }

    // Canceling an order by a customer
    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($order->customerID === $user->userID, 403);
        abort_unless($order->status === Order::STATUS_WAITING, 404);

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return back()->with('success', 'سفارش با موفقیت لغو شد.');
    }

    // Customer review on order after it's finished
    public function review(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($order->customerID === $user->userID, 403);
        abort_unless($order->status === Order::STATUS_FINISHED, 404);
        abort_if(! $order->needsReview(), 409, 'برای این سفارش قبلاً نظر ثبت شده.');

        // Validate the given data
        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ], [
            'rating.required'  => 'لطفاً یک امتیاز بین ۱ تا ۵ انتخاب کن.',
            'rating.between'   => 'امتیاز باید بین ۱ تا ۵ باشه.',
            'comment.required' => 'لطفاً نظرت رو بنویس.',
            'comment.max'      => 'نظر نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',
        ]);

        // Update the order
        $order->update([
            'rating'  => $data['rating'],
            'comment' => $data['comment'],
        ]);

        // Redirect
        return back()->with('success', 'نظر و امتیازت با موفقیت ثبت شد. ممنون بابت وقتی که گذاشتی 🙏');
    }

    // Make sure the order owner is correct
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


    // Return the company that user (role = 3, 4) is assigend to -> null if it's not
    private function companyOf(User $user): ?Company
    {
        return $user->companyAdmin?->company;
    }
}
