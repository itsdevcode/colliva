<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['campaign_id', 'creator_profile_id', 'proposal', 'quote_minor'])]
class CampaignApplication extends Model
{
/** @use HasFactory<\Database\Factories\CampaignApplicationFactory> */
use HasFactory;
/** @return BelongsTo<Campaign, $this> */
public function campaign(): BelongsTo
{ return $this->belongsTo(Campaign::class); }
/** @return BelongsTo<CreatorProfile, $this> */
public function creatorProfile(): BelongsTo
{ return $this->belongsTo(CreatorProfile::class); }
}
