<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['collaboration_id', 'notes', 'url'])]
class CollaborationDeliverable extends Model
{
/** @use HasFactory<\Database\Factories\CollaborationDeliverableFactory> */
use HasFactory;
/** @return BelongsTo<Collaboration, $this> */
public function collaboration(): BelongsTo
{ return $this->belongsTo(Collaboration::class); }
}
