<?php

namespace App\Livewire;

use App\Actions\ManageSupportTicket;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class SupportContact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $userType = 'other';

    public string $category = 'general';

    public string $subject = '';

    public string $description = '';

    public string $relatedReference = '';

    public string $website = '';

    #[Locked]
    public string $reference = '';

    #[Locked]
    public bool $emailQueued = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user ? $user->name : '';
        $this->email = $user ? $user->email : '';
        $this->userType = in_array(auth()->user()?->role, ['creator', 'brand', 'customer'], true) ? auth()->user()->role : 'other';
    }

    public function submit(ManageSupportTicket $workflow): void
    {
        if ($this->reference !== '') {
            return;
        }
        $key = 'support-submit:'.hash('sha256', (string) request()->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('subject', 'Too many requests. Please try again in '.RateLimiter::availableIn($key).' seconds.');

            return;
        }
        RateLimiter::hit($key, 600);
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'userType' => ['required', Rule::in(['creator', 'brand', 'customer', 'other'])],
            'category' => ['required', Rule::in(array_keys(SupportTicket::CATEGORIES))],
            'subject' => ['required', 'string', 'min:5', 'max:160'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            'relatedReference' => ['nullable', 'string', 'max:120'],
            'website' => ['max:0'],
        ]);
        $ticket = new SupportTicket([
            'name' => $this->name, 'email' => auth()->check() ? auth()->user()->email : $this->email,
            'user_type' => $this->userType, 'category' => $this->category,
            'subject' => $this->subject, 'description' => $this->description,
            'related_reference' => $this->relatedReference ?: null,
        ]);
        $ticket->user()->associate(auth()->user());
        $ticket->save();
        $this->reference = $ticket->reference;
        $this->emailQueued = $workflow->notify($ticket, 'Your support request has been received.');
    }

    public function render(): View
    {
        return view('livewire.support-contact')->title('Contact support');
    }
}
