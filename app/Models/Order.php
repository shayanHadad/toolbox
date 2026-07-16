<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'orderID';

    protected $fillable = [
        'customerID',
        'providerID',
        'companyID',
        'status',
        'comment',
        'rating',
        'order_date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerID', 'userID');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'providerID', 'userID');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }
}
