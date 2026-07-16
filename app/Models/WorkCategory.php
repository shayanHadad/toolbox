<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCategory extends Model
{
    protected $primaryKey = 'categoryID';

    protected $fillable = [
        'category_name',
    ];

    public function experts()
    {
        return $this->hasMany(ExpertDetail::class, 'categoryID', 'categoryID');
    }

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'company_categories',
            'categoryID',
            'companyID'
        );
    }
}
