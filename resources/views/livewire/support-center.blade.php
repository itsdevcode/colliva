<div class="container section support-page" x-data>
    <section class="support-hero" aria-labelledby="support-heading">
        <span class="eyebrow">The Colliva Support Center</span>
        <h1 id="support-heading">Hi! How can we help you?</h1>
        <p>Find answers, explore helpful guides, or contact our support team.</p>
        <div class="support-search" role="search" aria-label="Help articles">
            <label class="sr-only" for="support-search">Search support articles</label>
            <x-market-icon name="search" />
            <input id="support-search" x-ref="supportSearch" type="search" wire:model.live.debounce.250ms="search" placeholder="Search help articles…" maxlength="150" aria-controls="help-articles" @keydown.escape.prevent="$wire.clearSearch(); $refs.supportSearch.focus()">
            <button type="button" class="support-search-clear" wire:click="clearSearch" x-show="$wire.search.length > 0" x-cloak @click="$refs.supportSearch.focus()" aria-label="Clear search"><span aria-hidden="true">×</span></button>
        </div>
        <div class="support-shortcuts">
            <a href="{{ route('support.contact') }}">Contact support <x-market-icon name="arrow-up-right" /></a>
            <a href="{{ route('support.tickets') }}">My support tickets <x-market-icon name="arrow-right" /></a>
        </div>
    </section>

    <section aria-labelledby="support-topics">
        <div class="section-heading"><div><span class="eyebrow">A little guidance goes a long way</span><h2 id="support-topics">Find your starting point.</h2></div></div>
        <div class="support-category-grid">
            @foreach($categories as $topic)
                <button type="button" wire:key="topic-{{ $topic->id }}" wire:click="$set('category', '{{ $topic->slug }}')" @click="document.getElementById('help-articles').scrollIntoView({ block: 'start' })" @class(['support-category', 'is-selected' => $category === $topic->slug]) aria-controls="help-articles" aria-pressed="{{ $category === $topic->slug ? 'true' : 'false' }}">
                    <span class="support-topic-icon"><x-market-icon :name="['getting-started' => 'sparkles', 'creator-help' => 'camera', 'brand-help' => 'briefcase', 'customer-help' => 'heart', 'account-security' => 'shield', 'payments-billing' => 'globe'][$topic->slug] ?? 'globe'" /></span>
                    <h3>{{ $topic->name }}</h3><p>{{ $topic->description }}</p>
                    <span class="text-link">Explore guides <x-market-icon name="arrow-right" /></span>
                </button>
            @endforeach
        </div>
    </section>

    <section id="help-articles" class="support-articles" aria-labelledby="article-heading">
        <div class="section-heading">
            <div><span class="eyebrow">Knowledge, shared</span><h2 id="article-heading">{{ $search !== '' || $category !== '' ? 'Answers for you.' : 'Helpful guides.' }}</h2></div>
            @if($search !== '' || $category !== '')<button type="button" class="text-link" wire:click="clearFilters">Show all guides <x-market-icon name="arrow-right" /></button>@endif
        </div>
        <div class="support-results-status" role="status">
            <span wire:loading.remove>{{ $articles->count() }} {{ Str::plural('guide', $articles->count()) }}{{ $search !== '' || $category !== '' ? ' matching your search' : ' across our help topics' }}</span>
            <span wire:loading.delay>Finding helpful guides…</span>
        </div>
        <p class="support-offline" wire:offline role="alert">You’re offline. Reconnect to search for the latest help articles.</p>
        <div wire:loading.class="opacity-50">
            @forelse($categories->filter(fn ($topic) => $articleGroups->has($topic->id)) as $topic)
                <section class="support-article-group" wire:key="guide-group-{{ $topic->id }}" aria-labelledby="guide-group-{{ $topic->id }}">
                    <div class="support-group-heading"><h3 id="guide-group-{{ $topic->id }}">{{ $topic->name }}</h3><p>{{ $topic->description }}</p></div>
                    <div class="support-article-grid">
                        @foreach($articleGroups->get($topic->id) as $article)
                            <a class="support-article-link" wire:key="article-{{ $article->id }}" href="{{ route('support.article', $article->slug) }}">
                                <h4>{{ $article->title }}</h4><p>{{ $article->summary }}</p>
                                <span class="text-link">Read guide <x-market-icon name="arrow-right" /></span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @empty
                <x-colliva-empty title="No answers found just yet." description="Try a shorter search, choose another topic, or contact our support team for help.">
                    <x-colliva-button wire:click="clearFilters" variant="outline">Show all guides</x-colliva-button>
                </x-colliva-empty>
            @endforelse
        </div>
    </section>

    <section class="support-contact-banner"><div><span class="eyebrow">People, here to help</span><h2>Let’s work through it together.</h2><p>Tell us what’s happening. Keep your ticket reference handy for follow-ups.</p></div><x-colliva-button :href="route('support.contact')">Contact Support <x-market-icon name="arrow-right" /></x-colliva-button></section>
    <section class="support-faq"><h2>A few quick answers.</h2><details><summary>Why is my creator profile not visible?</summary><p>Public profiles need approval, publication consent, a verified account email, a photo, location, category, public portfolio work and a service package. Top Influencers also shows creators available for collaborations when that eligibility setting is enabled.</p></details><details><summary>Does Colliva handle collaboration payments?</summary><p>Payments are agreed and handled directly between collaborators. Colliva does not currently hold funds, offer escrow or process creator payouts. Contact support for platform billing questions.</p></details><details><summary>Can I contact support without an account?</summary><p>Yes. Guests can submit a request and receive a reference. Account holders can also read replies and send follow-ups in My Support Tickets. Guest tickets are not automatically linked to a later account.</p></details></section>
</div>
