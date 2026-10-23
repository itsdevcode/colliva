<?php

namespace App\Models;

use Database\Factories\CampaignApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['campaign_id', 'creator_profile_id', 'proposal', 'quote_minor'])]
class CampaignApplication extends Model
{
    /** @use HasFactory<CampaignApplicationFactory> */
    use HasFactory;

    /** @return HasOne<CollaborationRequest, $this> */
    public function collaborationRequest(): HasOne
    {
        return $this->hasOne(CollaborationRequest::class);
    }

    /** @return BelongsTo<Campaign, $this> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return BelongsTo<CreatorProfile, $this> */
    public function creatorProfile(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class);
    }
}
