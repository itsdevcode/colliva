<?php

namespace App\Models;

use Database\Factories\CollaborationRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property CustomerProfile|null $customerProfile
 * @property BrandProfile|null $brandProfile
 */
#[Fillable(['brand_profile_id', 'customer_profile_id', 'creator_profile_id', 'campaign_application_id', 'title', 'scope', 'price_minor', 'delivery_deadline'])]
class CollaborationRequest extends Model
{
    /** @use HasFactory<CollaborationRequestFactory> */
    use HasFactory;

    /** @return BelongsTo<BrandProfile, $this> */
    public function brandProfile(): BelongsTo
    {
        return $this->belongsTo(BrandProfile::class);
    }

    /** @return BelongsTo<CreatorProfile, $this> */
    public function creatorProfile(): BelongsTo
    {
        return $this->belongsTo(CreatorProfile::class);
    }

    /** @return HasOne<Collaboration, $this> */
    public function collaboration(): HasOne
    {
        return $this->hasOne(Collaboration::class);
    }

    /** @return BelongsTo<CustomerProfile, $this> */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function hirer(): User
    {
        if ($this->customer_profile_id !== null) {
            return $this->customerProfile->user;
        }

        return $this->brandProfile->user;
    }

    protected function casts(): array
    {
        return ['delivery_deadline' => 'date', 'creator_confirmed_at' => 'datetime', 'brand_confirmed_at' => 'datetime'];
    }
}
