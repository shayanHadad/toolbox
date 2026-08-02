<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'messageID';

    // For Eloquent to know this column does not exist in database
    const UPDATED_AT = null;

    protected $fillable = [
        'senderID',
        'receiverID',
        'message',
        'status',
        'companyID',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'senderID')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiverID')->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID', 'companyID');
    }

    public static function companyIdForUser(?User $user): ?int
    {
        // Make sure the user is not null and its role is 3, 4
        if (!$user || !in_array((int) $user->role, [3, 4], true)) {
            return null;
        }

        // Return null if company admin does not exist
        // Return the company ID if existed
        return $user->companyAdmin?->companyID;
    }

    // Resolve company ID
    public static function resolveCompanyId(User $senderUser, User $receiverUser): ?int
    {
        // Return first one if not null 
        // Else return the second one
        return static::companyIdForUser($senderUser) ?? static::companyIdForUser($receiverUser);
    }


    // Filter the messages belong to a company
    public function scopeForCompany(Builder $query, int $companyID): Builder
    {
        return $query->where('companyID', $companyID);
    }

    // Filter personal chats (between role 1, 2)
    public function scopeBetweenUsers(Builder $query, int $userA, int $userB): Builder
    {
        // Company ID is null
        // Fetch all the messages between User A and B
        return $query->whereNull('companyID')
            ->where(function (Builder $q) use ($userA, $userB) {
                $q->where(function (Builder $q2) use ($userA, $userB) {
                    $q2->where('senderID', $userA)->where('receiverID', $userB);
                })->orWhere(function (Builder $q2) use ($userA, $userB) {
                    $q2->where('senderID', $userB)->where('receiverID', $userA);
                });
            });
    }
}
