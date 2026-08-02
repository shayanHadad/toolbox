<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAdmin extends Model
{
    protected $primaryKey = 'adminID';

    protected $fillable = [
        'companyID',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_admin_id', 'adminID');
    }
}
