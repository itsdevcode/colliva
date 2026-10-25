<?php

namespace App\Models;

use Database\Factories\SupportArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['support_category_id', 'title', 'slug', 'summary', 'content', 'status', 'sort_order', 'published_at'])]
class SupportArticle extends Model
{
    /** @use HasFactory<SupportArticleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /** @return BelongsTo<SupportCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class, 'support_category_id');
    }

    /** @param Builder<SupportArticle> $query
     * @return Builder<SupportArticle> */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
