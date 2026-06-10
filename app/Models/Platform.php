<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_platform', 'platform_id', 'title_id');
    }
}