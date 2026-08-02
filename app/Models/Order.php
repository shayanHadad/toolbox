<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'orderID';

    // Status codes stored in database
    public const STATUS_WAITING     = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_FINISHED    = 3;
    public const STATUS_REJECTED    = 4;
    public const STATUS_CANCELLED   = 5;

    protected $fillable = [
        'customerID',
        'providerID',
        'companyID',
        'status',
        'details',
        'comment',
        'rating',
        'order_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    // Persian label for order statuses
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING     => 'در انتظار تأیید',
            self::STATUS_IN_PROGRESS => 'در حال انجام',
            self::STATUS_FINISHED    => 'تمام شده',
            self::STATUS_REJECTED    => 'رد شده',
            self::STATUS_CANCELLED   => 'لغو شده',
            default                  => 'وضعیت نامشخص #' . $this->status,
        };
    }

    // cd-badges for css classes based on status
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING     => 'cd-badge-waiting',
            self::STATUS_IN_PROGRESS => 'cd-badge-progress',
            self::STATUS_FINISHED    => 'cd-badge-done',
            self::STATUS_REJECTED    => 'cd-badge-rejected',
            self::STATUS_CANCELLED   => 'cd-badge-rejected',
            default                  => 'cd-badge-waiting',
        };
    }

    /**
     * آیا این سفارش تمام‌شده و هنوز مشتری براش نظر/امتیازی ثبت نکرده؟
     */
    public function needsReview(): bool
    {
        return $this->status === self::STATUS_FINISHED
            && is_null($this->comment)
            && is_null($this->rating);
    }

    // Automatically changed the status from in progress to finished if the order date passed
    public static function autoFinishPastOrders(): void
    {
        static::where('status', self::STATUS_IN_PROGRESS)
            ->whereDate('order_date', '<', now()->toDateString())
            ->update(['status' => self::STATUS_FINISHED]);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerID', 'userID')->withTrashed();
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'providerID', 'userID')->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }
}
