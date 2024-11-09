<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileName',
        'filePath',
        'isAvailable',
    ];
    /**
     * Get all of the requestApprovals for the File
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestApprovals(): HasMany
    {
        return $this->hasMany(RequestApproval::class, 'file_id');
    }
}
