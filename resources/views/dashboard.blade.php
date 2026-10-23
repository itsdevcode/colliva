<x-layouts::marketplace title="Your workspace">
<div class="container section workspace-page">
<span class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</span>
<h1>Welcome back, <span class="serif-word">{{ auth()->user()->name }}.</span>
</h1>
<p class="quiet" style="margin-top:15px">A little organisation. A lot more room to create.</p>@if($profile)<div class="workspace-card" style="margin-top:30px">
<span class="eyebrow">{{ auth()->user()->role === 'creator' ? 'PROFILE PUBLICATION' : 'YOUR BUSINESS' }}</span>
<h2>{{ $profile->display_name ?? $profile->company_name }}</h2>@if(auth()->user()->role === 'creator')<span class="status-badge">{{ $profile->status }}</span>
<p style="margin-top:15px">{{ $profile->moderation_note ?? 'Complete your profile and submit it for review. Only approved, consenting profiles appear in discovery.' }}</p>@endif</div>@else<div class="notice" style="background:#f0ebff;margin-top:30px">Your profile is waiting for its first chapter. Set it up below to get started.</div>@endif<div class="dashboard-metrics">@foreach($metrics as $label=>$value)<div>
<strong>{{ $value }}</strong>
<span>{{ $label }}</span>
</div>@endforeach</div>
<div class="dashboard-grid">@if(auth()->user()->role === 'creator')<a href="{{ route('creator.studio') }}">
<x-market-icon name="camera"/>
<h2>Your creator studio</h2>
<p>Build your profile, curate your portfolio and publish your services.</p>
</a>@elseif(auth()->user()->role === 'brand')<a href="{{ route('brand.workspace') }}">
<x-market-icon name="briefcase"/>
<h2>Your brand workspace</h2>
<p>Tell your story, manage business details and revisit saved creators.</p>
</a>@else<a href="{{ route('filament.admin.pages.dashboard') }}">
<x-market-icon name="shield"/>
<h2>Administration</h2>
<p>Review profiles, moderate campaigns and manage the marketplace.</p>
</a>@endif<a href="{{ route('campaigns') }}">
<x-market-icon name="globe"/>
<h2>Campaign opportunities</h2>
<p>Bring the right people and the right ideas together.</p>
</a>
<a href="{{ route('collaborations') }}">
<x-market-icon name="sparkles"/>
<h2>Collaborations</h2>
<p>Briefs, agreements, deliverables and notifications, all in one place.</p>
</a>
</div>
<a class="text-link" href="{{ route('profile.edit') }}">Account settings & deletion <x-market-icon name="arrow-up-right"/>
</a>
</div>
</x-layouts::marketplace>
