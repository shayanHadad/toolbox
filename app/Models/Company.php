<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $primaryKey = 'companyID';

    protected $fillable = [
        'name',
        'descriptions',
        'founding_date',
    ];

    protected function casts(): array
    {
        return [
            'founding_date' => 'date',
        ];
    }

    public function admins()
    {
        return $this->hasMany(CompanyAdmin::class, 'companyID', 'companyID');
    }

    public function categories()
    {
        return $this->belongsToMany(
            WorkCategory::class,
            'company_categories',
            'companyID',
            'categoryID'
        );
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'companyID', 'companyID');
    }

    /**
     * سفارش‌های این شرکت که برای $user (نماینده‌ی شرکت) قابل مشاهده‌ست:
     * - مالک شرکت (role=4): همه‌ی سفارش‌ها، بدون هیچ محدودیتی.
     * - ادمین شرکت (role=3): فقط سفارش‌هایی که هنوز تأیید یا رد نشدن
     *   (status = waiting)؛ به محض تأیید یا ردشدن، از دیدش خارج می‌شن.
     */
    public function ordersVisibleTo(User $user)
    {
        $query = $this->orders();

        if ((int) $user->role === 3) {
            $query->where('status', Order::STATUS_WAITING);
        }

        return $query;
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(
            User::class,
            'company_bookmarks',
            'companyID',
            'customerID'
        );
    }

    /**
     * یه کاربر ادمین این شرکت که بشه بهش پیام داد (اولین ادمینِ در دسترس).
     * اگه شرکت هنوز هیچ کاربر ادمینی نداشته باشه null برمی‌گرده.
     */
    public function contactUser(): ?User
    {
        foreach ($this->admins as $admin) {
            $user = $admin->users->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * مالک این شرکت (role=4)؛ برخلاف contactUser() که فقط اولین ادمینِ
     * در دسترس رو برمی‌گردونه، این متد مشخصاً دنبال کاربری با role=4
     * می‌گرده. برای پنل ادمین کل لازمه که مالک هر شرکت رو نشون بده.
     * از property access استفاده می‌کنه (نه متد admins())، تا اگه
     * رابطه‌ی admins.users از قبل eager-load شده باشه، کوئری تکراری
     * نزنه.
     */
    public function owner(): ?User
    {
        foreach ($this->admins as $admin) {
            $user = $admin->users->firstWhere('role', 4);
            if ($user) {
                return $user;
            }
        }

        return null;
    }
}
