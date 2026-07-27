<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Title extends Model
{
    protected $fillable = [
        'api_source',
        'api_id',
        'type',
        'title',
        'slug',
        'synopsis',
        'cover_image',
        'release_date',
        'status',
        'submitted_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'title_genre', 'title_id', 'genre_id');
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'title_platform', 'title_id', 'platform_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function watchlistEntries(): HasMany
    {
        return $this->hasMany(WatchlistEntry::class);
    }

    public function gamingEntries(): HasMany
    {
        return $this->hasMany(GamingEntry::class);
    }

    public function favouritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourites', 'title_id', 'user_id')->withTimestamps();
    }

    public function averageScore(): ?float
    {
        return $this->reviews()->avg('score');
    }
}