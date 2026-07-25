<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'messageID';

    const UPDATED_AT = null;

    protected $fillable = [
        'senderID',
        'receiverID',
        'message',
        'status',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'senderID', 'userID');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiverID', 'userID');
    }
}
