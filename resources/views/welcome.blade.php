<x-layouts::marketplace>
    <section class="hero container">
        <div class="hero-copy">
            <span class="pill"><span class="location-dot"></span>Local talent. New possibilities.</span>
            <h1>Great brands deserve great creators.</h1>
            <p>Discover talented influencers and UGC creators, explore their portfolios, and collaborate with the right people for your brand.</p>
            <div class="hero-actions">
                <x-colliva-button :href="route('creators')">Explore Creators <x-market-icon name="arrow-right" /></x-colliva-button>
                <x-colliva-button :href="route('register', ['role' => 'creator'])" variant="outline">Join as Creator</x-colliva-button>
            </div>
            <p class="local-caption"><x-market-icon name="pin" />Starting in Yamunanagar &amp; Jagadhri, Haryana.</p>
        </div>
    </section>

    <section class="discovery-strip container" aria-label="Find creators">
        <form action="{{ route('creators') }}" method="get">
            <label class="search-field">
                <x-market-icon name="search" />
                <span class="search-field-copy"><span class="field-caption">Find your creator</span><input type="search" name="search" placeholder="Name or creative style"></span>
            </label>
            <x-discovery-picker name="city" label="Location" placeholder="All locations" :options="$locations" />
            <x-discovery-picker name="category" label="Category" placeholder="All categories" :options="$categories" />
            <x-colliva-button type="submit">Find Creators</x-colliva-button>
        </form>
    </section>

    @if($creators->isNotEmpty())
        <div class="hero-gallery container" aria-label="Work from our creator community">
            @foreach($creators as $creator)
                @php($work = $creator->portfolioItems->firstWhere('image_path', '!=', null))
                @if($work)<a href="{{ route('creator.show', $creator->slug) }}"><img src="{{ Storage::disk(config('filesystems.media_disk'))->url($work->image_path) }}" alt="{{ $work->title }} by {{ $creator->display_name }}" width="600" height="750" loading="lazy"></a>@endif
            @endforeach
        </div>
    @endif

    <section class="section container featured-section">
        <x-colliva-heading eyebrow="Meet your next creative partner" title="Original voices. Real possibility." description="Explore portfolios, compare services, and find a style that feels right for your brand.">
            <a class="text-link" href="{{ route('creators') }}">View all creators <x-market-icon name="arrow-right" /></a>
        </x-colliva-heading>
        <div class="creator-grid">
            @forelse($creators as $creator)
                <x-creator-card :creator="$creator" />
            @empty
                <x-colliva-empty title="The first voices start with you." description="Our local creator community is taking shape. Create your profile, share your original work, and be ready for your next collaboration.">
                    <x-colliva-button :href="route('register', ['role' => 'creator'])">Create Your Profile <x-market-icon name="arrow-right" /></x-colliva-button>
                    <a class="text-link" href="{{ route('home') }}#how-it-works">See how Colliva works</a>
                </x-colliva-empty>
            @endforelse
        </div>
    </section>

    <section class="category-section">
        <div class="container">
            <x-colliva-heading eyebrow="Made for your next idea" title="Find your creative direction." description="From everyday stories to standout campaigns, discover the right kind of talent." />
            <div class="category-grid">
                @foreach($categories as $category)
                    <a class="category-tile" href="{{ route('creators', ['category' => $category->slug]) }}"><x-market-icon name="camera" /><span>{{ $category->name }}</span><x-market-icon name="arrow-up-right" /></a>
                @endforeach
            </div>
            <div class="location-section">
                <h2>Closer to your audience.</h2>
                <p class="quiet">Discover creators who know the places and people you do.</p>
                <div class="location-links">@foreach($locations as $location)<a href="{{ route('creators', ['city' => $location->slug]) }}"><x-market-icon name="pin" />{{ $location->name }}<x-market-icon name="arrow-up-right" /></a>@endforeach</div>
            </div>
        </div>
    </section>

    <section class="section container" id="how-it-works">
        <div class="center-heading"><span class="eyebrow">How Colliva works</span><h2>From a first look<br>to a shared vision.</h2></div>
        <div class="steps-grid">
            @foreach([['01', 'Discover your people', 'Search by location, category, budget, and platform. Find talent that understands your audience.'], ['02', 'Explore their work', 'Get a feel for their style through original portfolios, social profiles, and clearly priced services.'], ['03', 'Create together', 'Send a brief, agree on the details, and keep your collaboration and deliverables in one place.']] as [$number, $heading, $body])
                <article><span class="step-number">{{ $number }}<x-market-icon name="arrow-right" /></span><h3>{{ $heading }}</h3><p>{{ $body }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="container two-sided">
        <div id="for-brands">
            <span class="eyebrow">For brands</span><h2>A fresh perspective.<br>A natural connection.</h2>
            <p>Find people who can bring your story to life. Compare real work and services, save your favourites, and start with a clear brief.</p>
            <x-colliva-button :href="route('register', ['role' => 'brand'])" variant="dark">Find Your Creative Partner <x-market-icon name="arrow-right" /></x-colliva-button>
            <div class="benefit-tags"><span><x-market-icon name="check" />Reviewed profiles</span><span><x-market-icon name="check" />Clear service pricing</span><span><x-market-icon name="check" />Direct collaboration</span></div>
        </div>
        <div id="for-creators">
            <span class="eyebrow">For creators</span><h2>Your best work.<br>Your next opportunity.</h2>
            <p>Build a portfolio that feels like you. Set your services, share your strengths, and connect with brands looking for your perspective.</p>
            <x-colliva-button :href="route('register', ['role' => 'creator'])">Join as Creator <x-market-icon name="arrow-right" /></x-colliva-button>
            <span class="creator-cta-note">Your work. Your rates. Your creative identity.</span>
        </div>
    </section>

    <section class="section container faq-section" id="faq">
        <div><span class="eyebrow">A little clarity</span><h2>Good questions.<br>Straight answers.</h2></div>
        <div class="faq-list">
            @foreach([['Who can join Colliva?', 'Influencers, UGC creators, photographers and other original content creators can apply. Brands and businesses can register to discover creators and publish campaign briefs.'], ['Where is Colliva available?', 'We’re starting with Yamunanagar, Jagadhri and nearby Haryana cities, with room to grow across India.'], ['How do payments work?', 'You and your collaborator agree on payment terms and coordinate payments directly. Colliva does not collect payments, provide escrow or guarantee payment.'], ['How are profiles reviewed?', 'Creators submit their portfolio, services and publication consent. An administrator reviews profiles before they appear publicly. Social metrics are marked as self-reported unless independently verified.'], ['How do I get started as a creator?', 'Create an account, verify your email, add your profile, original work and service packages, then submit for review. You can save a draft at any time.']] as [$question, $answer])
                <details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>
            @endforeach
        </div>
    </section>
</x-layouts::marketplace>
