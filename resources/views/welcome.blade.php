<x-layouts::marketplace>
<div class="market-home">
    <section class="market-hero">
        <div class="container">
            <span class="market-kicker"><span class="location-dot"></span>THE RIGHT PEOPLE. YOUR NEXT BIG IDEA.</span>
            <h1>Great brands deserve<br><span>great creators.</span></h1>
            <p class="market-hero-description">Find local influencers and UGC creators. Explore their work and make something great together.</p>

            <div class="hero-actions home-primary-actions flex flex-col gap-3 sm:flex-row">
                <x-colliva-button :href="route('creators')">Explore Creators <x-market-icon name="arrow-right" /></x-colliva-button>
                <a class="text-link" href="{{ route('for-creators') }}">Join as Creator</a>
            </div>
            <form class="home-search" action="{{ route('creators') }}" method="get" role="search">
                <label class="sr-only" for="home-creator-search">Search creators by name or style</label>
                <div class="home-search-entry flex items-center gap-2">
                    <x-market-icon name="search" /><input id="home-creator-search" type="search" name="search" placeholder="Name or creative style" enterkeyhint="search" autocomplete="off">
                    <button type="submit" class="icon-button" aria-label="Search creators"><x-market-icon name="arrow-right" /></button>
                </div>
                <div class="home-search-shortcuts">
                    <details class="home-location"><summary><x-market-icon name="pin" />Location &amp; category</summary><div class="home-search-options grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <label>City<select name="city"><option value="">Any city</option>@foreach($locations as $location)<option value="{{ $location->slug }}">{{ $location->name }}</option>@endforeach</select></label>
                        <label>Category<select name="category"><option value="">Any category</option>@foreach($categories as $category)<option value="{{ $category->slug }}">{{ $category->name }}</option>@endforeach</select></label>
                        <label>Platform<select name="platform"><option value="">All platforms</option><option value="instagram">Instagram</option><option value="youtube">YouTube</option><option value="ugc">UGC</option></select></label>
                    </div></details>
                    <button type="submit" name="filters" value="open" class="text-link"><x-market-icon name="filter" />Filters</button>
                </div>
            </form>
            <div class="market-quick-links"><span>Popular searches</span><a href="{{ route('creators', ['category' => 'fashion']) }}">Fashion</a><a href="{{ route('creators', ['category' => 'beauty']) }}">Beauty</a><a href="{{ route('creators', ['platform' => 'ugc']) }}">UGC</a><a href="{{ route('creators', ['budget' => 5000]) }}">Under ₹5,000</a><a href="{{ route('creators', ['available' => 1]) }}">Available now <x-market-icon name="arrow-up-right" /></a></div>
        </div>
    </section>

    <section class="container market-featured">
        @if($creators->isNotEmpty())
            <div class="market-section-heading"><div><h2>Meet your next creative partner.</h2><p>Explore original portfolios and clearly priced services.</p></div><a class="text-link" href="{{ route('creators') }}">View all creators <x-market-icon name="arrow-right" /></a></div>
            <div class="creator-grid">@foreach($creators as $creator)<x-creator-card :creator="$creator" />@endforeach</div>
        @else
            <div class="market-launch">
                <span class="launch-icon"><x-market-icon name="sparkles" /></span>
                <div><h2>The first voices start with you.</h2><p>We’re building our local creator community. Share your work and be ready for your next opportunity.</p></div>
                <x-colliva-button :href="route('for-creators')" variant="outline">Join as Creator <x-market-icon name="arrow-right" /></x-colliva-button>
            </div>
        @endif
    </section>

    <section class="container market-browse">
        <div class="market-section-heading"><div><h2>A creator for every kind of brand.</h2><p>Start with a category. Find a perspective you love.</p></div><a class="text-link" href="{{ route('creators') }}">Explore all creators <x-market-icon name="arrow-right" /></a></div>
        <div class="market-category-grid">
            @foreach([['fashion', 'Fashion', 'Style with a point of view', 'fashion-creators.webp'], ['food-restaurants', 'Food & Restaurants', 'Stories worth a second serving', 'food-creators.webp'], ['beauty', 'Beauty', 'A fresh take on your everyday', 'beauty-still-life.webp'], ['ugc-creators', 'UGC Creators', 'Content that feels like a conversation', 'creative-studio.webp']] as [$slug, $name, $description, $image])
                @if($categories->contains('slug', $slug))
                    <a class="market-category-card" href="{{ route('creators', ['category' => $slug]) }}">
                        <div class="market-category-photo"><x-editorial-image :name="$image" :alt="'Illustrative '.strtolower($name).' content creation scene'" sizes="(min-width: 1100px) 280px, (min-width: 600px) 45vw, 90vw" /><span class="category-explore"><x-market-icon name="arrow-up-right" /></span></div>
                        <h3>{{ $name }}</h3><p>{{ $description }}</p>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="market-category-footer"><div class="market-category-links">@foreach($categories as $category)<a href="{{ route('creators', ['category' => $category->slug]) }}">{{ $category->name }}</a>@endforeach</div><small>Category imagery is illustrative.</small></div>
    </section>

    <section class="market-workflow" id="how-it-works">
        <div class="container">
            <div class="market-section-heading"><div><span class="eyebrow">LESS SEARCHING. MORE COLLABORATING.</span><h2>From discovery to delivery.<br>All in one place.</h2></div><a class="button button-outline" href="{{ route('for-brands') }}">Get Started <x-market-icon name="arrow-right" /></a></div>
            <div class="market-workflow-grid">
                @foreach([['01', 'search', 'Find your fit', 'Explore creators by platform, category, city, and budget. Look through their work before you reach out.'], ['02', 'briefcase', 'Make the brief yours', 'Share your idea, agree on the deliverables, and set expectations together. Keep the details clear.'], ['03', 'check', 'Bring it to life', 'Review submitted content, request revisions, and manage your collaboration in your workspace.']] as [$number, $icon, $heading, $body])
                    <article><div class="workflow-card-top"><span class="workflow-icon"><x-market-icon :name="$icon" /></span><span>{{ $number }}</span></div><h3>{{ $heading }}</h3><p>{{ $body }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container market-brand-section" id="for-brands">
        <div class="market-brand-copy"><span class="eyebrow">FOR BRANDS &amp; LOCAL BUSINESSES</span><h2>Your next campaign starts<br>with the right connection.</h2><p>Find people who understand your audience. Compare their work, keep a shortlist, and collaborate with a clear plan.</p><ul><li><x-market-icon name="check" />Original portfolios and transparent service pricing</li><li><x-market-icon name="check" />Saved creators, campaign briefs, and applications</li><li><x-market-icon name="check" />Agreements and deliverables in one workspace</li></ul><x-colliva-button :href="route('for-brands')">Find Your Creative Partner <x-market-icon name="arrow-right" /></x-colliva-button></div>
        <div class="market-workspace-preview" aria-label="Colliva collaboration workflow"><div class="preview-top"><span class="wordmark">colliva<span class="brand-dot">.</span></span><span class="status-badge">Your workspace</span></div><h3>A little clarity.<br>A lot more possibility.</h3><div class="preview-step"><span><x-market-icon name="search" /></span><div><strong>Discover &amp; shortlist</strong><small>Find the right style and audience.</small></div><x-market-icon name="check" /></div><div class="preview-step"><span><x-market-icon name="briefcase" /></span><div><strong>Brief &amp; agree</strong><small>Get the important details in place.</small></div><x-market-icon name="check" /></div><div class="preview-step"><span><x-market-icon name="camera" /></span><div><strong>Create &amp; review</strong><small>Keep your deliverables together.</small></div><x-market-icon name="check" /></div><p>Discovery. Briefs. Deliverables. Connected.</p></div>
    </section>

    <section class="container market-creator-section" id="for-creators">
        <div><span class="eyebrow">FOR THE CREATIVELY INDEPENDENT</span><h2>You make the content.<br>We make the connections.</h2><p>Build your portfolio, set your rates, and get discovered by brands looking for a perspective like yours.</p><x-colliva-button :href="route('for-creators')" variant="white">Create Your Profile <x-market-icon name="arrow-right" /></x-colliva-button></div>
        <div class="creator-path"><span><x-market-icon name="camera" />Your portfolio</span><span><x-market-icon name="briefcase" />Your services</span><span><x-market-icon name="heart" />Your next collaboration</span><p>Your work. Your rates. Your creative identity.</p></div>
    </section>

    <section class="container market-local-section"><div><span class="eyebrow">LOCAL TALENT. REAL CONNECTIONS.</span><h2>Find creators close to home.</h2><p>Starting in Yamunanagar, Jagadhri, and across Haryana.</p></div><div class="location-links">@foreach($locations as $location)<a href="{{ route('creators', ['city' => $location->slug]) }}"><x-market-icon name="pin" />{{ $location->name }}<x-market-icon name="arrow-up-right" /></a>@endforeach</div></section>

    <section class="container market-faq" id="faq"><div><span class="eyebrow">BEFORE YOU GET STARTED</span><h2>A few things<br>worth knowing.</h2></div><div class="faq-list">@foreach([['Who can join Colliva?', 'Influencers, UGC creators, photographers and other original content creators can apply. Brands and businesses can register to discover creators and publish campaign briefs.'], ['How are creator profiles reviewed?', 'Creators submit their portfolio, services and publication consent. An administrator reviews profiles before they appear publicly. Social metrics are marked as self-reported unless independently verified.'], ['How do payments work?', 'You and your collaborator agree on payment terms and coordinate payments directly. Colliva does not collect payments, provide escrow or guarantee payment.'], ['How do I get started as a creator?', 'Create an account, verify your email, add your profile, original work and service packages, then submit for review. You can save a draft at any time.']] as [$question, $answer])<details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>@endforeach</div></section>
</div>
</x-layouts::marketplace>
