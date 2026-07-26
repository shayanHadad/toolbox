<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBookmark extends Model
{
    protected $primaryKey = 'companyBookmarkID';

    protected $fillable = [
        'customerID',
        'companyID',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerID', 'userID');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }
}
