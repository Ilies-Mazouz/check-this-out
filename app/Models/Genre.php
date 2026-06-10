<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
    ];

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'title_genre', 'genre_id', 'title_id');
    }
}