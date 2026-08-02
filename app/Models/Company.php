<?php
//--//
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

    // Return all the company admins (Even ones that got soft-deleted)
    public function repUserIds(): array
    {
        return $this->admins()
            ->with(['users' => function ($q) { // Even the users that got soft deleted
                return $q->withTrashed();
            }])
            ->get()
            ->flatMap->users // Create a flat map instead of having a matrix
            ->pluck('userID')
            ->all();
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

    // Return orders based on role
    // Role = 4 -> All the orders
    // Role = 3 -> Only orders with Waiting status
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

    // Return the first available admin
    public function contactUser(): ?User // Output is a user or it's null
    {
        foreach ($this->admins as $admin) {
            $user = $admin->users->first();
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    // Return the company owner 
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
