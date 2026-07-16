<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'userID';

    protected $fillable = [
        'username',
        'password',
        'contact_number',
        'role',
        'register_date',
        'first_name',
        'last_name',
        'date_of_birth',
        'profile_picture',
        'company_admin_id',
    ];

    protected $hidden = [
        'password',
    ];

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
}
