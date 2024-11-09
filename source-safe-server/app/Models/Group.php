<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupName',
        'groupImage',
        'owner_id'
    ];

    /**
     * Get all of the groupMambers for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupMambers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    /**
     * Get all of the files for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'group_id');
    }

    /**
     * The users that belong to the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }
}
