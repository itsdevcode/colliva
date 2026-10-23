<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'creator_profile_id', 'reason'])]
class ContentReport extends Model
{
/** @use HasFactory<\Database\Factories\ContentReportFactory> */
use HasFactory;
/** @return BelongsTo<CreatorProfile, $this> */
public function creatorProfile(): BelongsTo
{ return $this->belongsTo(CreatorProfile::class); }
/** @return BelongsTo<User, $this> */
public function user(): BelongsTo
{ return $this->belongsTo(User::class); }
}
