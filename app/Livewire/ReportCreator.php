<?php

namespace App\Livewire;

use App\Models\ContentReport;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReportCreator extends Component
{
    #[Locked]
    public int $creatorId;

    public string $reason = '';

    public bool $sent = false;

    public function send(): void
    {
        abort_unless(auth()->user()?->hasVerifiedEmail(), 403);
        $this->validate(['reason' => ['required', 'string', 'min:20', 'max:2000']]);
        abort_if(RateLimiter::tooManyAttempts('report:'.auth()->id(), 5), 429);
        $creator = CreatorProfile::publiclyVisible()->findOrFail($this->creatorId);
        ContentReport::create(['user_id' => auth()->id(), 'creator_profile_id' => $creator->id, 'reason' => $this->reason]);
        RateLimiter::hit('report:'.auth()->id(), 3600);
        $this->reset('reason');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.report-creator');
    }
}
