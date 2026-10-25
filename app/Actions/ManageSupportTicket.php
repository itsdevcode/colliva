<?php

namespace App\Actions;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class ManageSupportTicket
{
    public function respond(User $actor, SupportTicket $ticket, string $body, bool $internal = false): void
    {
        DB::transaction(function () use ($actor, $ticket, $body, $internal): void {
            $ticket = SupportTicket::whereKey($ticket->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize($internal ? 'update' : 'reply', $ticket);
            Validator::make(['body' => $body], ['body' => ['required', 'string', 'min:5', 'max:10000']])->validate();
            $message = $ticket->messages()->make(['body' => $body]);
            $message->user()->associate($actor);
            $message->internal = $internal;
            $message->staff = $actor->role === 'admin';
            $message->save();
            $ticket->last_activity_at = now();
            if (! $internal) {
                $ticket->status = $actor->role === 'admin' ? 'waiting_for_customer' : 'open';
                $ticket->status_changed_at = now();
            }
            $ticket->save();
        });
        if ($actor->role === 'admin' && ! $internal) {
            $this->notify($ticket, $body);
        }
    }

    public function status(User $actor, SupportTicket $ticket, string $status): void
    {
        Gate::forUser($actor)->authorize('update', $ticket);
        Validator::make(['status' => $status], ['status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))]])->validate();
        $ticket->forceFill(['status' => $status, 'last_activity_at' => now(), 'status_changed_at' => now()])->save();
    }

    public function notify(SupportTicket $ticket, string $message): bool
    {
        if (! in_array(config('mail.default'), ['smtp', 'ses', 'postmark', 'resend', 'mailgun', 'sendmail'], true)) {
            return false;
        }
        try {
            Notification::route('mail', $ticket->email)->notify(new SupportTicketUpdate($ticket->reference, $message, $ticket->user_id !== null));

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
