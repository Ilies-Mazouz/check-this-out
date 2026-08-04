<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'is_admin',
        'theme',
        'birthday',
        'bio',
        'avatar',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'birthday' => 'date',
        ];
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function watchlistEntries(): HasMany
    {
        return $this->hasMany(WatchlistEntry::class);
    }

    public function gamingEntries(): HasMany
    {
        return $this->hasMany(GamingEntry::class);
    }

    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id');
    }

    public function titleSubmissions(): HasMany
    {
        return $this->hasMany(Title::class, 'submitted_by');
    }

    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'favourites', 'user_id', 'title_id')->withTimestamps();
    }

    public function hasBlocked(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_user_id', $user->id)->exists();
    }
}
