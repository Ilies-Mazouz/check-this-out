<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsComment extends Model
{
    protected $fillable = [
        'user_id',
        'news_article_id',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newsArticle(): BelongsTo
    {
        return $this->belongsTo(NewsArticle::class);
    }
}
