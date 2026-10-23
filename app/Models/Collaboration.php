<?php

namespace App\Models;

use Database\Factories\CollaborationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['collaboration_request_id'])]
class Collaboration extends Model
{
    /** @use HasFactory<CollaborationFactory> */
    use HasFactory;

    /** @return BelongsTo<CollaborationRequest, $this> */
    public function request(): BelongsTo
    {
        return $this->belongsTo(CollaborationRequest::class, 'collaboration_request_id');
    }

    /** @return HasMany<CollaborationDeliverable, $this> */
    public function deliverables(): HasMany
    {
        return $this->hasMany(CollaborationDeliverable::class);
    }

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }
}
