<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'isAdmin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Get all of the groupMembers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    /**
     * Get all of the ownedGroups for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * Get all of the files for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'user_id');
    }

    /**
     * Get all of the createdRequests for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdRequests(): HasMany
    {
        return $this->hasMany(RequestApproval::class, 'user_id');
    }

    /**
     * Get all of the adminRequests for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminRequests(): HasMany
    {
        return $this->hasMany(RequestApproval::class, 'owner_id');
    }

    /**
     * The groups that belong to the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id');
    }
}
