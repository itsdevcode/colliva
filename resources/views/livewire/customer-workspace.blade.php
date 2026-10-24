<div class="container section workspace-page customer-workspace">
    <span class="eyebrow">YOUR PERSONAL CREATIVE SPACE</span>
    <h1>A little inspiration,<br>a world of possibility.</h1>
    <p class="quiet">Welcome, {{ auth()->user()->name }}. Keep your favourite talent and personal projects close.</p>
    <div class="customer-project-invite"><div><span class="eyebrow">WHAT HAVE YOU GOT IN MIND?</span><h2>Your next idea deserves a creative partner.</h2><p>An event, a passion project, or something entirely your own. Start with a portfolio that speaks to you.</p></div><x-colliva-button :href="route('creators')">Browse creators <x-market-icon name="arrow-right" /></x-colliva-button></div>
    <div class="dashboard-grid">
        <a href="{{ route('collaborations') }}"><x-market-icon name="heart" /><h2>Personal enquiries <span>{{ $pendingEnquiries }}</span></h2><p>Follow the conversations around your next project.</p></a>
        <a href="{{ route('collaborations') }}"><x-market-icon name="sparkles" /><h2>Active collaborations <span>{{ $activeCollaborations }}</span></h2><p>Agreed plans, delivery, and reviews. All together.</p></a>
        <a href="{{ route('profile.edit') }}"><x-market-icon name="shield" /><h2>Account settings</h2><p>Manage your details, security, and preferences.</p></a>
    </div>
    <section id="saved-creators" class="role-section"><span class="eyebrow">YOUR CREATIVE SHORTLIST</span><h2>People to come back to.</h2><div class="creator-grid">@forelse($savedCreators as $creator)<div wire:key="saved-{{ $creator->id }}"><x-creator-card :creator="$creator" /><button class="text-link" wire:click="removeSaved({{ $creator->id }})" wire:loading.attr="disabled">Remove from saved creators</button></div>@empty<x-colliva-empty title="Your next favourite is out there." description="Save creators while browsing and find them here whenever inspiration strikes."><x-colliva-button :href="route('creators')" variant="outline">Explore creators</x-colliva-button></x-colliva-empty>@endforelse</div></section>
</div>
