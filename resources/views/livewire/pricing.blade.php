<div class="container pricing-page">
    <header class="pricing-intro">
        <span class="eyebrow">A CLEAR START, FOR EVERYONE</span>
        <h1>Pricing &amp; <span>Fees.</span></h1>
        <p>Free to join. Free to explore.<br>Creator collaborations are priced separately.</p>
    </header>

    <section class="grid grid-cols-1 gap-6 md:grid-cols-2" aria-label="Free platform access">
        <article class="pricing-card flex flex-col" aria-labelledby="creator-access-heading">
            <span class="role-symbol"><x-market-icon name="camera" /></span>
            <h2 id="creator-access-heading">For Creators</h2>
            <p class="pricing-card-description">A place for your work, your perspective, and your next creative connection.</p>
            <div class="pricing-access"><strong>Free</strong><span>Platform access</span></div>
            <ul class="pricing-features">
                @foreach(['Creator registration', 'Creating and maintaining your profile', 'Building and showcasing your portfolio', 'Receiving and responding to enquiries'] as $feature)
                    <li><x-market-icon name="check" /><span>{{ $feature }}</span></li>
                @endforeach
            </ul>
            <div class="pricing-card-actions">
                <x-colliva-button :href="route('signup', 'creator')">Join as Creator <x-market-icon name="arrow-right" /></x-colliva-button>
                <p>Your creative work has its own value. Set and agree your collaboration rates separately.</p>
            </div>
        </article>

        <article class="pricing-card flex flex-col" aria-labelledby="hiring-access-heading">
            <span class="role-symbol"><x-market-icon name="briefcase" /></span>
            <h2 id="hiring-access-heading">For Brands &amp; Customers</h2>
            <p class="pricing-card-description">Find a creative partner for your business, personal project, or next big idea.</p>
            <div class="pricing-access"><strong>Free</strong><span>Platform access</span></div>
            <ul class="pricing-features">
                @foreach(['Brand and Customer registration', 'Discovering creators', 'Viewing creator profiles and portfolios', 'Sending initial enquiries'] as $feature)
                    <li><x-market-icon name="check" /><span>{{ $feature }}</span></li>
                @endforeach
            </ul>
            <div class="pricing-card-actions">
                <x-colliva-button :href="route('signup', 'brand')">Join as Brand <x-market-icon name="arrow-right" /></x-colliva-button>
                <a class="text-link" href="{{ route('signup', 'customer') }}">Hiring for yourself? Join as Customer <x-market-icon name="arrow-right" /></a>
            </div>
        </article>
    </section>

    <section class="pricing-explanation" aria-labelledby="creator-rates-heading">
        <span class="role-symbol"><x-market-icon name="sparkles" /></span>
        <div>
            <span class="eyebrow">PLATFORM ACCESS &amp; CREATOR WORK ARE DIFFERENT</span>
            <h2 id="creator-rates-heading">Free access. Independent creator rates.</h2>
            <p>Free platform access does not mean free creator work. Creator collaboration prices are independent of platform access. Each creator sets their own service rates, and you agree the final scope and price together.</p>
            <p>Rates can vary with deliverables, usage rights, revisions, and turnaround time. Review the portfolio and service packages, then discuss the details before work begins.</p>
            <a class="text-link" href="{{ route('creators') }}">Explore creators &amp; their services <x-market-icon name="arrow-right" /></a>
        </div>
    </section>

    <section class="pricing-payment-note" aria-labelledby="payment-terms-heading">
        <x-market-icon name="shield" />
        <div><h2 id="payment-terms-heading">Agree the details directly.</h2><p>You and the creator agree on the scope, price, and payment terms directly. Colliva does not collect payments or provide escrow. No subscription or trial is required for the free access described above.</p></div>
    </section>

    <section class="role-faq pricing-faq" aria-labelledby="pricing-faq-heading">
        <div><span class="eyebrow">GOOD QUESTIONS. CLEAR ANSWERS.</span><h2 id="pricing-faq-heading">A little more clarity.</h2></div>
        <div>
            @foreach([
                ['Is registration free?', 'Yes. Creator, Brand, and Customer registration is free. Use the signup flow for your account type to get started.'],
                ['Do creators pay for a profile or portfolio?', 'No. Creator profiles and portfolios are free, as are receiving and responding to enquiries.'],
                ['Do Brands or Customers pay to discover creators?', 'No. Creator discovery, viewing profiles and portfolios, and sending initial enquiries are free for Brands and Customers.'],
                ['Does free access include the cost of a collaboration?', 'No. A creator’s collaboration fee pays for their work. Their rates are separate from Colliva platform access and are agreed directly with them.'],
                ['How much do creators charge?', 'Each creator sets their own rates. Look at their service packages and discuss your project, deliverables, usage rights, and deadline to agree a price. Colliva does not set a universal collaboration price.'],
                ['Could platform fees change in the future?', 'This page describes current free access. Any future platform fees or optional paid features will be explained clearly before they become available. No future paid plan is currently offered on this page.'],
            ] as [$question, $answer])
                <details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>
            @endforeach
        </div>
    </section>

    <div class="pricing-outro"><p>A creative connection starts with a conversation.</p><a class="text-link" href="{{ route('get-started') }}">Find your place on Colliva <x-market-icon name="arrow-right" /></a></div>
</div>
