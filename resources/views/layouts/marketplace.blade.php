<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Great brands deserve great creators.' }} — Colliva</title>
<meta name="description" content="{{ $description ?? 'Discover local influencers and UGC creators in Yamunanagar, Jagadhri and across Haryana. Find your creative partner on Colliva.' }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="{{ $title ?? 'Discover your next creative partner' }} — Colliva">
<meta property="og:description" content="Discover local talent. Create something extraordinary.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@auth <meta name="robots" content="noindex,nofollow"> @endauth
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
@fonts
@vite(['resources/css/app.css','resources/js/app.js'])
@livewireStyles
</head>
<body class="marketplace antialiased">
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header" x-data="{ scrolled: window.scrollY > 12 }" @scroll.window.passive="scrolled = window.scrollY > 12" :class="{ 'is-scrolled': scrolled }">
    <a class="wordmark" href="{{ route('home') }}" aria-label="Colliva home">colliva<span class="brand-dot">.</span></a>
    <nav class="desktop-nav" aria-label="Main">
        <a href="{{ route('creators') }}">Discover Creators</a>
        <a href="{{ route('for-creators') }}">For Creators</a>
        <a href="{{ route('for-brands') }}">For Brands</a>
        <a href="{{ route('home') }}#how-it-works">How It Works</a>
    </nav>
    <div class="nav-actions">
        @auth
            <x-colliva-button :href="route('dashboard')" variant="outline" :small="true">My Workspace</x-colliva-button>
        @else
            <a class="login-link" href="{{ route('login') }}">Login</a>
            <x-colliva-button :href="route('get-started')" :small="true">Get Started</x-colliva-button>
        @endauth
        <button type="button" class="menu-toggle icon-button" @click="$refs.navigation.showModal()" aria-label="Open navigation" aria-haspopup="dialog"><x-market-icon name="menu" /></button>
    </div>
    <x-colliva-drawer name="navigation" title="Navigation">
        <nav class="drawer-links" aria-label="Mobile">
            <a href="{{ route('creators') }}">Discover Creators</a>
            <a href="{{ route('home') }}#how-it-works" @click="$refs.navigation.close()">How It Works</a>
            <a href="{{ route('for-brands') }}" @click="$refs.navigation.close()">For Brands</a>
            <a href="{{ route('for-creators') }}" @click="$refs.navigation.close()">For Creators</a>
            @auth
                <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard', '*.dashboard')) aria-current="page" @endif>My Workspace</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <x-colliva-button :href="route('get-started')">Get Started</x-colliva-button>
            @endauth
        </nav>
    </x-colliva-drawer>
</header>
@auth
<nav class="workspace-nav" aria-label="Workspace">
<a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard', '*.dashboard')) aria-current="page" @endif>Overview</a>@if(auth()->user()->role === 'creator')<a href="{{ route('creator.studio') }}" @if(request()->routeIs('creator.studio')) aria-current="page" @endif>My creator studio</a>@elseif(auth()->user()->role === 'brand')<a href="{{ route('brand.workspace') }}" @if(request()->routeIs('brand.workspace')) aria-current="page" @endif>Business & saved creators</a>
<a href="{{ route('creators') }}">Discover creators</a>@elseif(auth()->user()->role === 'customer')<a href="{{ route('creators') }}">Browse creators</a><a href="{{ route('customer.dashboard') }}#saved-creators">Saved creators</a>@else<a href="{{ route('filament.admin.pages.dashboard') }}">Administration</a>@endif@if(auth()->user()->role !== 'customer')<a href="{{ route('campaigns') }}" @if(request()->routeIs('campaigns')) aria-current="page" @endif>Campaigns</a>@endif
<a href="{{ route('collaborations') }}" @if(request()->routeIs('collaborations')) aria-current="page" @endif>Collaborations & notifications</a>
<a href="{{ route('profile.edit') }}" @if(request()->routeIs('profile.edit')) aria-current="page" @endif>Settings</a>
<form action="{{ route('logout') }}" method="post">@csrf<button>Log out</button>
</form>
</nav>
@endauth
<main id="main">{{ $slot }}</main>
<footer class="site-footer">
<div class="footer-top">
<div>
<a class="wordmark" href="{{ route('home') }}">colliva<span class="brand-dot">.</span>
</a>
<p>Great brands. Original creators.<br>A better place to create together.</p>
</div>
<div>
<span class="eyebrow">DISCOVER</span>
<a href="{{ route('creators') }}">Discover creators</a>
<a href="{{ route('creators',['city'=>'yamunanagar']) }}">Yamunanagar</a>
<a href="{{ route('creators',['city'=>'jagadhri']) }}">Jagadhri</a>
</div>
<div>
<span class="eyebrow">GET STARTED</span>
<a href="{{ route('for-brands') }}">For brands</a>
<a href="{{ route('for-creators') }}">For creators</a>
<a href="{{ route('for-customers') }}">For customers</a>
<a href="{{ route('home') }}#faq">FAQs</a>
</div>
<div class="footer-note">
<span class="location-dot">
</span> STARTING IN HARYANA<br>
<span>Open to extraordinary possibilities.</span>
</div>
</div>
<div class="footer-bottom">
<span>© {{ date('Y') }} Colliva. All rights reserved.</span>
<span>Real people. Original work. Local impact.</span>
<a href="{{ route('home') }}#main">Back to top ↑</a>
</div>
</footer>
@livewireScripts
@fluxScripts
</body>
</html>
