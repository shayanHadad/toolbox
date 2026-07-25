<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'userID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'password',
        'contact_number',
        'role',
        'first_name',
        'last_name',
        'date_of_birth',
        'profile_picture',
        'company_admin_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    public function expertDetail()
    {
        return $this->hasOne(ExpertDetail::class, 'userID', 'userID');
    }

    public function companyAdmin()
    {
        return $this->belongsTo(CompanyAdmin::class, 'company_admin_id', 'adminID');
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customerID', 'userID');
    }

    public function providerOrders()
    {
        return $this->hasMany(Order::class, 'providerID', 'userID');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'senderID', 'userID');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiverID', 'userID');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'customerID', 'userID');
    }

    public function bookmarkedProviders()
    {
        return $this->belongsToMany(
            User::class,
            'bookmarks',
            'customerID',
            'providerID'
        );
    }

    public function dashboardRoute(): string
    {
        return match ((int) $this->role) {
            0 => 'dashboard.admin',
            1 => 'dashboard.customer',
            2 => 'dashboard.expert',
            3 => 'dashboard.company',
            default    => 'home',
        };
    }
    public function isCustomer()
{
    return $this->role == 1;
}
}
