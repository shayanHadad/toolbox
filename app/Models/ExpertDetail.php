<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertDetail extends Model
{
    protected $primaryKey = 'userID';

    public $incrementing = false;

    protected $fillable = [
        'userID',
        'categoryID',
        'description',
        'resume',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function category()
    {
        return $this->belongsTo(WorkCategory::class, 'categoryID', 'categoryID');
    }
}
