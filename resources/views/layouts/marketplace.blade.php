<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Local talent. Extraordinary possibilities.' }} — Shoutly</title>
<meta name="description" content="{{ $description ?? 'Discover local influencers and UGC creators in Yamunanagar, Jagadhri and across Haryana. Find your creative partner on Shoutly.' }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="{{ $title ?? 'Find your kind of creator' }} — Shoutly">
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
<header class="site-header" x-data="{ open: false }">
 <a class="wordmark" href="{{ route('home') }}" aria-label="Shoutly home">
<span class="brand-mark">s<span>✳</span>
</span>shoutly<span class="brand-dot">.</span>
</a>
 <nav class="desktop-nav" aria-label="Main">
<a href="{{ route('creators') }}">Explore creators</a>
<a href="{{ route('home') }}#how-it-works">How it works</a>
<a href="{{ route('home') }}#for-brands">For brands</a>
<a href="{{ route('home') }}#for-creators">For creators</a>
</nav>
 <div class="nav-actions">@auth <a class="text-link" href="{{ route('dashboard') }}">My workspace <x-market-icon name="arrow-up-right" />
</a> @else <a class="login-link" href="{{ route('login') }}">Log in</a>
<a class="button button-dark button-small" href="{{ route('register',['role'=>'creator']) }}">Join as creator <x-market-icon name="arrow-up-right" />
</a> @endauth <button class="menu-toggle" @click="open = !open" :aria-expanded="open" aria-label="Toggle menu">
<x-market-icon name="menu" />
</button>
</div>
 <nav class="mobile-nav" x-cloak x-show="open" @keydown.escape.window="open = false" aria-label="Mobile">
<a href="{{ route('creators') }}">Explore creators</a>
<a href="{{ route('home') }}#how-it-works">How it works</a>
<a href="{{ route('login') }}">Log in</a>
<a href="{{ route('register',['role'=>'brand']) }}">Join as brand</a>
</nav>
</header>
@auth
<nav class="workspace-nav" aria-label="Workspace">
<a href="{{ route('dashboard') }}">Overview</a>@if(auth()->user()->role === 'creator')<a href="{{ route('creator.studio') }}">My creator studio</a>@elseif(auth()->user()->role === 'brand')<a href="{{ route('brand.workspace') }}">Business & saved creators</a>
<a href="{{ route('creators') }}">Discover creators</a>@else<a href="{{ route('filament.admin.pages.dashboard') }}">Administration</a>@endif<a href="{{ route('campaigns') }}">Campaigns</a>
<a href="{{ route('collaborations') }}">Collaborations & notifications</a>
<a href="{{ route('profile.edit') }}">Settings</a>
<form action="{{ route('logout') }}" method="post">@csrf<button>Log out</button>
</form>
</nav>
@endauth
<main id="main">{{ $slot }}</main>
<footer class="site-footer">
<div class="footer-top">
<div>
<a class="wordmark" href="{{ route('home') }}">shoutly<span class="brand-dot">.</span>
</a>
<p>Small cities. Big creative energy.<br>Made for connections that mean more.</p>
</div>
<div>
<span class="eyebrow">DISCOVER</span>
<a href="{{ route('creators') }}">Explore creators</a>
<a href="{{ route('creators',['city'=>'yamunanagar']) }}">Yamunanagar</a>
<a href="{{ route('creators',['city'=>'jagadhri']) }}">Jagadhri</a>
</div>
<div>
<span class="eyebrow">GET STARTED</span>
<a href="{{ route('register',['role'=>'brand']) }}">For brands</a>
<a href="{{ route('register',['role'=>'creator']) }}">For creators</a>
<a href="{{ route('home') }}#faq">FAQs</a>
</div>
<div class="footer-note">
<span class="location-dot">
</span> ROOTED IN HARYANA<br>
<span>Open to extraordinary possibilities.</span>
</div>
</div>
<div class="footer-bottom">
<span>© {{ date('Y') }} Shoutly. All rights reserved.</span>
<span>Real people. Original work. Local impact.</span>
<a href="{{ route('home') }}#main">Back to top ↑</a>
</div>
</footer>
@livewireScripts
</body>
</html>
