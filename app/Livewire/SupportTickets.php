<?php

namespace App\Livewire;

use App\Actions\ManageSupportTicket;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.marketplace')]
class SupportTickets extends Component
{
    use WithPagination;

    #[Locked]
    public ?string $reference = null;

    public string $body = '';

    public function mount(?string $reference = null): void
    {
        abort_unless(auth()->check(), 403);
        $this->reference = $reference;
    }

    public function reply(ManageSupportTicket $workflow): void
    {
        $ticket = $this->ownedTicket();
        Gate::authorize('reply', $ticket);
        $this->validate(['body' => ['required', 'string', 'min:5', 'max:10000']]);
        $key = 'support-reply:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('body', 'Please wait a minute before sending another message.');

            return;
        }
        RateLimiter::hit($key, 60);
        $workflow->respond(auth()->user(), $ticket, $this->body);
        $this->reset('body');
        session()->flash('status', 'Your message has been added.');
    }

    private function ownedTicket(): SupportTicket
    {
        $ticket = SupportTicket::where('user_id', auth()->id())->where('reference', $this->reference)->firstOrFail();
        Gate::authorize('view', $ticket);

        return $ticket;
    }

    public function render(): View
    {
        abort_unless(auth()->check(), 403);
        $ticket = $this->reference !== null ? $this->ownedTicket() : null;

        return view('livewire.support-tickets', [
            'ticket' => $ticket,
            'messages' => $ticket?->messages()->where('internal', false)->orderBy('id')->paginate(20, pageName: 'messagesPage'),
            'tickets' => $ticket ? null : SupportTicket::where('user_id', auth()->id())->orderByDesc('last_activity_at')->orderByDesc('id')->paginate(12),
        ])->title($ticket ? 'Support ticket '.$ticket->reference : 'My Support Tickets');
    }
}
