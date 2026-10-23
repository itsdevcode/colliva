<?php

namespace App\Models;

use Database\Factories\PortfolioItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['creator_profile_id', 'title', 'description', 'image_path', 'video_url', 'rights_confirmed_at'])]
class PortfolioItem extends Model
{
    /** @use HasFactory<PortfolioItemFactory> */
    use HasFactory;

    /** @return BelongsTo<CreatorProfile, $this> */
    public function creatorProfile(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class);
    }

    protected function casts(): array
    {
        return ['rights_confirmed_at' => 'datetime', 'hidden' => 'boolean'];
    }
}
