<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'note'])]
class AuditLog extends Model
{
/** @use HasFactory<\Database\Factories\AuditLogFactory> */
use HasFactory;
/** @return BelongsTo<User, $this> */
public function user(): BelongsTo
{ return $this->belongsTo(User::class); }
}
