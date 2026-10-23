<?php

namespace App\Models;

use Database\Factories\CreatorProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'location_id', 'category_id', 'display_name', 'slug', 'bio', 'photo_path', 'cover_path', 'available'])]
class CreatorProfile extends Model
{
    /** @use HasFactory<CreatorProfileFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'creator_category');
    }

    /** @return BelongsToMany<Language, $this> */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'creator_language');
    }

    /** @return HasMany<SocialAccount, $this> */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /** @return HasMany<PortfolioItem, $this> */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    /** @return HasMany<ServicePackage, $this> */
    public function servicePackages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }

    /** @return HasMany<CampaignApplication, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    protected function casts(): array
    {
        return ['available' => 'boolean', 'is_demo' => 'boolean', 'publication_consented_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    /** @param Builder<CreatorProfile> $query
     * @return Builder<CreatorProfile> */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', 'approved')->whereNotNull('publication_consented_at')->whereNotNull('location_id')->whereNotNull('category_id')->where('is_demo', false)->whereNotNull('photo_path')->whereHas('user', fn ($user) => $user->whereNotNull('email_verified_at')->where('role', 'creator'))->whereHas('servicePackages')->whereHas('portfolioItems', fn ($item) => $item->where('hidden', false));
    }
}
