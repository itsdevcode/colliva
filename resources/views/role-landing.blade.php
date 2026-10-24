<x-layouts::marketplace :title="$page['headline']" :description="$page['description']">
    <div class="role-landing role-landing-{{ $role }}">
        <section class="container role-hero">
            <div class="role-hero-copy">
                <span class="eyebrow">{{ $page['eyebrow'] }}</span>
                <h1>{{ $page['headline'] }}</h1>
                <p>{{ $page['description'] }}</p>
                <div class="role-hero-actions">
                    <x-colliva-button :href="route('signup', $role)">Create {{ ucfirst($role) }} Account <x-market-icon name="arrow-right" /></x-colliva-button>
                    <a class="text-link" href="{{ $role === 'creator' ? '#opportunities' : route('creators') }}">{{ $page['secondary'] }} <x-market-icon name="arrow-up-right" /></a>
                </div>
                <span class="role-footnote">Original work. Real connections. Room for you.</span>
            </div>
            <figure class="role-hero-visual">
                <x-editorial-image :name="$page['image']" :priority="true" sizes="(min-width: 900px) 45vw, 90vw" />
                <figcaption><span class="eyebrow">THE POSSIBILITIES START WITH YOU</span><strong>{{ $page['imageLabel'] }}</strong><small>Illustrative imagery</small></figcaption>
            </figure>
        </section>
        <section class="role-band">
            <div class="container role-showcase">
                <div><span class="eyebrow">{{ $role === 'creator' ? 'YOUR PORTFOLIO, YOUR PERSPECTIVE' : 'DISCOVER YOUR CREATIVE PARTNER' }}</span><h2>{{ $page['section'] }}</h2><p>{{ $page['intro'] }}</p><a class="text-link" href="{{ route('creators') }}">Explore the creator community <x-market-icon name="arrow-right" /></a></div>
                <div class="role-preview">
                    <span class="role-symbol"><x-market-icon :name="$role === 'creator' ? 'camera' : 'search'" /></span>
                    <h3>{{ $role === 'creator' ? 'A home for your best work.' : 'Great work starts with the right person.' }}</h3>
                    <p>{{ $role === 'creator' ? 'Portfolio · Social channels · Services · Availability' : 'Explore by city · Creative category · Platform · Services' }}</p>
                    <div class="role-preview-tags"><span>Original portfolios</span><span>Local connections</span><span>Clear deliverables</span></div>
                </div>
            </div>
        </section>
        <section class="container role-section">
            <span class="eyebrow">{{ $role === 'customer' ? 'MADE FOR YOUR EVERYDAY POSSIBILITIES' : 'WHY JOIN COLLIVA' }}</span>
            <h2>{{ $page['benefitsTitle'] }}</h2>
            <div class="role-benefits">@foreach($page['benefits'] as [$icon, $title, $description])<article><span class="role-symbol"><x-market-icon :name="$icon" /></span><h3>{{ $title }}</h3><p>{{ $description }}</p></article>@endforeach</div>
        </section>
        @if($role !== 'creator')
            <section class="container role-section role-featured">
                <div class="section-heading"><div><span class="eyebrow">ORIGINAL PEOPLE. ORIGINAL WORK.</span><h2>{{ $role === 'customer' ? 'Find a style that speaks to you.' : 'Meet your next creative partner.' }}</h2></div><a class="text-link" href="{{ route('creators') }}">Browse creators <x-market-icon name="arrow-right" /></a></div>
                <div class="creator-grid">@forelse($creators as $creator)<x-creator-card :creator="$creator" />@empty<x-colliva-empty title="A creative community is taking shape." description="Approved creator portfolios will appear here. In the meantime, explore discovery or create your account." />@endforelse</div>
            </section>
        @endif
        <section class="role-band"><div class="container role-section"><span class="eyebrow">A SIMPLE START</span><h2>{{ $role === 'creator' ? 'From first upload to new possibilities.' : 'From an idea to something real.' }}</h2><div class="role-steps">@foreach($page['steps'] as [$title, $description])<article><span class="role-step-number">0{{ $loop->iteration }}</span><h3>{{ $title }}</h3><p>{{ $description }}</p></article>@endforeach</div></div></section>
        <section class="container role-section role-workflow" id="opportunities"><span class="eyebrow">{{ $role === 'creator' ? 'PACKAGES & OPPORTUNITIES' : ($role === 'brand' ? 'YOUR CAMPAIGN WORKSPACE' : 'YOUR PERSONAL CREATIVE SPACE') }}</span><h2>{{ $page['workflowTitle'] }}</h2><p>{{ $page['workflow'] }}</p><x-colliva-button :href="route('signup', $role)" variant="outline">{{ $role === 'creator' ? 'Create your portfolio' : 'Get started' }} <x-market-icon name="arrow-right" /></x-colliva-button></section>
        <section class="container role-section role-faq"><div><span class="eyebrow">A FEW THINGS TO KNOW</span><h2>Good questions.<br>Clear answers.</h2></div><div>@foreach($page['faqs'] as [$question, $answer])<details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>@endforeach</div></section>
        <section class="container role-final"><span class="eyebrow">LET'S MAKE SOMETHING HAPPEN</span><h2>{{ $page['final'] }}</h2><x-colliva-button :href="route('signup', $role)">Create {{ ucfirst($role) }} Account <x-market-icon name="arrow-right" /></x-colliva-button><a class="text-link" href="{{ route('get-started') }}">Explore other ways to join</a></section>
    </div>
</x-layouts::marketplace>
