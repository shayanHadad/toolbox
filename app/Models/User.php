<?php
//--//
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable; // So laravel can (login | logout | ...)
use Illuminate\Notifications\Notifiable; // If user needed to be notified

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'userID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'password',
        'contact_number',
        'role',
        'first_name',
        'last_name',
        'date_of_birth',
        'profile_picture',
        'company_admin_id',
    ];

    // This fields will not be shown in json
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting the types
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    ////////////////////////////////////// Relationships ////////////////////////////////////////////////
    public function expertDetail()
    {
        return $this->hasOne(ExpertDetail::class, 'userID', 'userID');
    }

    public function isAdmin(): bool
    {
        return (int) $this->role === 0;
    }

    public function isCustomer(): bool
    {
        return (int) $this->role === 1;
    }

    public function isExpert(): bool
    {
        return (int) $this->role === 2;
    }

    public function isCompanyAdmin(): bool
    {
        return (int) $this->role === 3;
    }

    public function isCompanyOwner(): bool
    {
        return (int) $this->role === 4;
    }

    public function isCompany(): bool
    {
        return in_array((int) $this->role, [3, 4], true);
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

    // Connecting to pivot table
    public function bookmarkedProviders()
    {
        return $this->belongsToMany(
            User::class,
            'bookmarks',
            'customerID',
            'providerID'
        );
    }

    public function companyBookmarks()
    {
        return $this->hasMany(CompanyBookmark::class, 'customerID', 'userID');
    }

    // Connecting to pivot table
    public function bookmarkedCompanies()
    {
        return $this->belongsToMany(
            Company::class,
            'company_bookmarks',
            'customerID',
            'companyID'
        );
    }

    // For redirecting to user dashboard based on role
    public function dashboardRoute(): string
    {
        return match ((int) $this->role) {
            0 => 'dashboard.admin',
            1 => 'dashboard.customer',
            2 => 'dashboard.expert',
            3, 4 => 'dashboard.company',
            default    => 'home',
        };
    }

    // Soft deleting the user
    public function anonymizeAndDelete(): void
    {
        // Add "deleted" to username and phone to free them up.
        $this->forceFill([
            'username'       => $this->username . '_deleted_' . $this->userID,
            'contact_number' => $this->contact_number . '_deleted_' . $this->userID,
        ])->save();

        $this->delete();
    }

    // Returning the profile picture url if user had any | return the default if not.
    public function profilePictureUrl(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        return asset(match (true) {
            in_array((int) $this->role, [0, 1], true) => 'images/default-pfp.png',
            in_array((int) $this->role, [2, 3], true) => 'images/expert.png',
            (int) $this->role === 4 => 'images/company.png',
            default => 'images/default-pfp.png',
        });
    }
}
