<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $primaryKey = 'bookmarkID';

    protected $fillable = [
        'customerID',
        'providerID',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerID', 'userID');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'providerID', 'userID');
    }
}
