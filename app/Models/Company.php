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
        'rating_avg',
    ];

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
}
