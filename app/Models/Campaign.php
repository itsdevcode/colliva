<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['brand_profile_id', 'category_id', 'title', 'description', 'deliverables', 'requirements', 'budget_minor', 'application_deadline', 'delivery_deadline'])]
class Campaign extends Model
{
/** @use HasFactory<\Database\Factories\CampaignFactory> */
use HasFactory;
/** @return BelongsTo<BrandProfile, $this> */
public function brandProfile(): BelongsTo
{ return $this->belongsTo(BrandProfile::class); }
/** @return BelongsTo<Category, $this> */
public function category(): BelongsTo
{ return $this->belongsTo(Category::class); }
/** @return BelongsToMany<static, $this> */
public function locations(): BelongsToMany
{ return $this->belongsToMany(Location::class); }
/** @return HasMany<CampaignApplication, $this> */
public function applications(): HasMany
{ return $this->hasMany(CampaignApplication::class); }
public function platforms(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(CampaignPlatform::class); }
protected function casts(): array { return ['application_deadline' => 'date', 'delivery_deadline' => 'date']; }
}
