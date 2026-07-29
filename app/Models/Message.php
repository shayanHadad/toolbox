<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * Primary key فعلی پروژه.
     */
    protected $primaryKey = 'messageID';

    /**
     * جدول messages فقط created_at دارد، updated_at ندارد. با null کردن
     * UPDATED_AT، Eloquent دیگر موقع update() سراغ آن ستون نمی‌رود؛
     * created_at همچنان به‌صورت خودکار موقع insert ست می‌شود.
     */
    const UPDATED_AT = null;

    /**
     * فیلدهای قابل پر شدن.
     */
    protected $fillable = [
        'senderID',
        'receiverID',
        'message',
        'status',
        'companyID',
    ];

    /*
    |--------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------
    */

    /**
     * withTrashed() اینجا لازمه چون بعد از soft-delete شدن یک کاربر
     * (مثلاً یک ادمین شرکت که حذف شده)، پیام‌های قدیمی همچنان باید طرف
     * حساب رو نشون بدن، نه اینکه sender/receiver یهو null بشه.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'senderID')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiverID')->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }

    /*
    |--------------------------------------------------------------------
    | Helpers برای تشخیص و resolve کردن companyID
    |--------------------------------------------------------------------
    */

    /**
     * اگر کاربر عضو شرکت باشد (role=3 ادمین یا role=4 مالک)، companyID او
     * را از طریق رابطه‌ی companyAdmin() که روی مدل User موجود است
     * برمی‌گرداند، در غیر این صورت null.
     */
    public static function companyIdForUser(?User $user): ?int
    {
        if (!$user || !in_array((int) $user->role, [3, 4], true)) {
            return null;
        }

        return $user->companyAdmin?->companyID;
    }

    /**
     * companyID مربوط به یک پیام بین دو کاربر را تشخیص می‌دهد.
     * اگر هیچ‌کدام از طرفین عضو شرکت نباشند، null برمی‌گردد (چت شخصی).
     */
    public static function resolveCompanyId(User $senderUser, User $receiverUser): ?int
    {
        return static::companyIdForUser($senderUser) ?? static::companyIdForUser($receiverUser);
    }

    /*
    |--------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------
    */

    /**
     * فیلتر پیام‌های مربوط به مکالمه‌ی مشترک یک شرکت.
     */
    public function scopeForCompany(Builder $query, int $companyID): Builder
    {
        return $query->where('companyID', $companyID);
    }

    /**
     * فیلتر پیام‌های چت شخصی (بدون شرکت) بین دو کاربر مشخص.
     */
    public function scopeBetweenUsers(Builder $query, int $userA, int $userB): Builder
    {
        return $query->whereNull('companyID')
            ->where(function (Builder $q) use ($userA, $userB) {
                $q->where(function (Builder $q2) use ($userA, $userB) {
                    $q2->where('senderID', $userA)->where('receiverID', $userB);
                })->orWhere(function (Builder $q2) use ($userA, $userB) {
                    $q2->where('senderID', $userB)->where('receiverID', $userA);
                });
            });
    }
}
