<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupName',
        'groupImage'
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
}
