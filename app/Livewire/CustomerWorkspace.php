<?php

namespace App\Livewire;

use App\Models\CollaborationRequest;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class CustomerWorkspace extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->role === 'customer', 403);
    }

    public function removeSaved(int $id): void
    {
        abort_unless(auth()->user()->role === 'customer', 403);
        auth()->user()->savedCreators()->detach($id);
    }

    public function render(): View
    {
        abort_unless(auth()->user()->role === 'customer', 403);
        $user = auth()->user();
        $requests = CollaborationRequest::whereHas('customerProfile', fn ($query) => $query->where('user_id', $user->id));

        return view('livewire.customer-workspace', [
            'savedCreators' => $user->savedCreators()->publiclyVisible()->with(['category', 'location', 'socialAccounts', 'portfolioItems' => fn ($query) => $query->where('hidden', false)])->withMin('servicePackages', 'price_minor')->get(),
            'pendingEnquiries' => (clone $requests)->whereIn('status', ['pending', 'changes_requested'])->count(),
            'activeCollaborations' => (clone $requests)->whereHas('collaboration', fn ($query) => $query->whereIn('status', ['active', 'submitted', 'revision_requested']))->count(),
        ])->title('Your personal creative space');
    }
}
