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
}
