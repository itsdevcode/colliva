<?php

namespace App\Models;

use Database\Factories\SupportCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class SupportCategory extends Model
{
    /** @use HasFactory<SupportCategoryFactory> */
    use HasFactory;

    /** @return HasMany<SupportArticle, $this> */
    public function articles(): HasMany
    {
        return $this->hasMany(SupportArticle::class);
    }
}
