<x-layouts::marketplace :title="ucfirst(auth()->user()->role).' dashboard'">
    <div class="container section workspace-page">
        <span class="eyebrow">YOUR {{ strtoupper(auth()->user()->role) }} SPACE</span>
        <h1>Welcome back, {{ auth()->user()->name }}.</h1>
        <p class="quiet" style="margin-top:15px">{{ auth()->user()->role === 'creator' ? 'Your work, your creative identity, and the opportunities ahead.' : 'Your creative partners, campaign plans, and collaborations. Together.' }}</p>
        <section class="workspace-card" style="margin-top:32px">
            @if(auth()->user()->role === 'creator')
                <div class="section-heading"><div><span class="eyebrow">YOUR CREATIVE IDENTITY</span><h2>{{ $profile->display_name }}</h2></div><span class="status-badge">{{ ucfirst($profile->status) }}</span></div>
                <label for="profile-completion">Profile completion · {{ $profileCompletion }}%</label>
                <progress id="profile-completion" max="100" value="{{ $profileCompletion }}" class="profile-completion">{{ $profileCompletion }}%</progress>
                <p>{{ $profile->moderation_note ?? 'Add your original work, social accounts, and services. Submit for review when you are ready to be discovered.' }}</p>
            @else
                <span class="eyebrow">YOUR BRAND</span><h2>{{ $profile->company_name }}</h2><p>{{ $profile->business_category ?? 'Your business, ready for its next creative connection.' }}</p>
            @endif
        </section>
        <div class="dashboard-metrics">@foreach($metrics as $label => $value)<div><strong>{{ $value }}</strong><span>{{ $label }}</span></div>@endforeach</div>
        <div class="dashboard-grid">
            @if(auth()->user()->role === 'creator')
                @foreach([['camera', 'Your portfolio', 'Curate original work that shows your perspective.', 4], ['briefcase', 'Service packages', 'Define your deliverables, pricing, and turnaround.', 5], ['sparkles', 'Profile & publication', 'Complete your story and manage publication.', 1]] as [$icon, $title, $description, $step])
                    <a href="{{ route('creator.studio', ['step' => $step]) }}"><x-market-icon :name="$icon" /><h2>{{ $title }}</h2><p>{{ $description }}</p></a>
                @endforeach
            @else
                <a href="{{ route('creators') }}"><x-market-icon name="search" /><h2>Creator discovery</h2><p>Find local talent by city, category, and creative style.</p></a>
                <a href="{{ route('brand.workspace') }}#saved-creators"><x-market-icon name="heart" /><h2>Saved creators</h2><p>Return to your creative shortlist and business profile.</p></a>
                <a href="{{ route('collaborations') }}"><x-market-icon name="briefcase" /><h2>Hiring activity</h2><p>Follow confirmed work, deliverables, and final reviews.</p></a>
            @endif
            <a href="{{ route('campaigns') }}"><x-market-icon name="globe" /><h2>{{ auth()->user()->role === 'creator' ? 'Campaign applications' : 'Your campaigns' }}</h2><p>{{ auth()->user()->role === 'creator' ? 'Explore published briefs and follow your applications.' : 'Draft briefs, review applications, and track campaign progress.' }}</p></a>
            <a href="{{ route('collaborations') }}"><x-market-icon name="sparkles" /><h2>Collaboration requests</h2><p>Review enquiries, agree on terms, and keep the work moving.</p></a>
        </div>
        <a class="text-link" href="{{ route('profile.edit') }}">Account settings <x-market-icon name="arrow-right" /></a>
    </div>
</x-layouts::marketplace>
