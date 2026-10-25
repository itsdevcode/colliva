<?php

namespace App\Models;

use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'user_type', 'category', 'subject', 'description', 'related_reference'])]
class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory;

    public const STATUSES = ['open' => 'Open', 'in_progress' => 'In Progress', 'waiting_for_customer' => 'Waiting for Customer', 'resolved' => 'Resolved', 'closed' => 'Closed'];

    public const CATEGORIES = ['account' => 'Account & Login', 'verification' => 'Creator Verification', 'profile' => 'Profile & Portfolio', 'campaigns' => 'Campaigns', 'collaborations' => 'Collaborations', 'billing' => 'Payments & Billing', 'technical' => 'Technical Issue', 'general' => 'General Question', 'feedback' => 'Feedback'];

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket): void {
            $ticket->reference ??= 'COL-'.Str::upper((string) Str::ulid());
            $ticket->last_activity_at ??= now();
        });
    }

    protected function casts(): array
    {
        return ['last_activity_at' => 'datetime', 'status_changed_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<SupportMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }
}
