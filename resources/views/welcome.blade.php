<x-layouts::marketplace>
<section class="hero container">
 <div class="hero-copy">
<span class="pill">
<span class="location-dot">
</span> LOCAL TALENT. LIMITLESS POSSIBILITIES.</span>
<h1>Big ideas.<br>Meet your<br>
<span class="serif-word">kind of creator.</span>
<span class="headline-star">✳</span>
</h1>
<p>Find creators who make your brand stand out.<br>Discover local influencers and UGC creators.<br>Make something that means something.</p>
<div class="hero-actions">
<a class="button button-purple" href="{{ route('creators') }}">Find your creator <x-market-icon name="arrow-up-right"/>
</a>
<a class="text-link" href="{{ route('register',['role'=>'creator']) }}">I’m a creator <x-market-icon name="arrow-right"/>
</a>
</div>
<div class="local-caption">
<x-market-icon name="pin"/>
<span>Starting in Yamunanagar & Jagadhri. Growing with you.</span>
</div>
</div>
 <div class="hero-art" aria-label="Original editorial illustration celebrating creative work">
  <div class="art-grid">
</div>
<div class="art-orbit">
</div>
<div class="art-label">THE NEXT BIG THING<br>COULD BE LOCAL.</div>
  <div class="poster poster-green">
<span class="poster-kicker">FRESH PERSPECTIVES</span>
<div class="camera-art">
<div class="camera-top">
</div>
<div class="camera-body">
<div class="camera-lens">
<div>
</div>
</div>
<span class="camera-flash">
</span>
<span class="camera-brand">MAKE / CREATE</span>
</div>
</div>
<span class="poster-bottom">A little local. A lot of original.</span>
</div>
  <div class="poster poster-purple">
<span class="poster-kicker">YOUR BRAND, REIMAGINED.</span>
<div class="poster-type">make<br>some<br>
<em>noise.</em>
</div>
<span class="poster-star">✳</span>
<span class="poster-bottom">CREATE SOMETHING DIFFERENT ↗</span>
</div>
  <div class="art-sticker">
<x-market-icon name="sparkles"/>
<div>Good things happen<br>
<strong>when you collaborate.</strong>
</div>
</div>
<div class="art-tag">Content with a local soul <span>↗</span>
</div>
<div class="art-footnote">A SPACE FOR ORIGINAL VOICES</div>
 </div>
</section>
<section class="discovery-strip container" aria-label="Find creators">
<form action="{{ route('creators') }}" method="get">
<label class="search-field">
<x-market-icon name="search"/>
<span class="sr-only">Search creators</span>
<input name="search" placeholder="What’s your next big idea?">
</label>
<label>
<span class="field-caption">LOCATION</span>
<select name="city">
<option value="">Anywhere in Haryana</option>@foreach($locations as $location)<option value="{{ $location->slug }}">{{ $location->name }}</option>@endforeach</select>
</label>
<label>
<span class="field-caption">CREATOR CATEGORY</span>
<select name="category">
<option value="">All kinds of creative</option>@foreach($categories as $category)<option value="{{ $category->slug }}">{{ $category->name }}</option>@endforeach</select>
</label>
<button class="button button-dark">
<x-market-icon name="search"/>
<span>Explore creators</span>
</button>
</form>
</section>
<div class="local-band container">
<span>BUILT FOR YOUR NEXT</span>
<span>restaurant launch <span>✳</span> product story <span>✳</span> brand moment <span>✳</span> big idea</span>
</div>
<section class="section container" id="creators">
<div class="section-heading">
<div>
<span class="eyebrow">THE PEOPLE BEHIND THE POSSIBILITIES</span>
<h2>Fresh voices. <span class="serif-word">Real connections.</span>
</h2>
<p>Discover the creative energy in your neighbourhood.</p>
</div>
<a class="text-link" href="{{ route('creators') }}">Explore all creators <x-market-icon name="arrow-up-right"/>
</a>
</div>
 @if($creators->isNotEmpty())<div class="creator-grid">@foreach($creators as $creator)<x-creator-card :creator="$creator"/>@endforeach</div>@else<div class="launch-empty">
<div class="empty-art">
<span>✳</span>
<span>your<br>next<br>
<em>chapter.</em>
</span>
</div>
<div>
<span class="eyebrow">A NEW CREATIVE COMMUNITY</span>
<h3>The first voices<br>start with you.</h3>
<p>We’re opening the doors to local talent. Approved creator portfolios will appear here as our community grows.</p>
<a class="button button-dark" href="{{ route('register',['role'=>'creator']) }}">Be an early creator <x-market-icon name="arrow-up-right"/>
</a>
</div>
<div class="empty-side-note">ORIGINAL WORK.<br>REAL PEOPLE.<br>
<span>COMING TOGETHER.</span>
</div>
</div>@endif
</section>
<section class="category-section">
<div class="container">
<div class="section-heading">
<div>
<span class="eyebrow">A CREATOR FOR EVERY IDEA</span>
<h2>Find your <span class="serif-word">creative lane.</span>
</h2>
</div>
<span class="quiet">Different talents. Same local spirit.</span>
</div>
<div class="category-grid">@foreach($categories as $category)<a class="category-tile category-{{ $loop->index % 4 }}" href="{{ route('creators',['category'=>$category->slug]) }}">
<span class="category-symbol">{{ ['◉','✳','✧','◒','↗','◎','⌘','▧','✺'][$loop->index % 9] }}</span>
<span>{{ $category->name }}</span>
<x-market-icon name="arrow-up-right"/>
</a>@endforeach</div>
</div>
</section>
<section class="section container" id="how-it-works">
<div class="center-heading">
<span class="eyebrow">LESS BACK AND FORTH. MORE MAKING.</span>
<h2>A great collaboration.<br>
<span class="serif-word">Without the complicated.</span>
</h2>
</div>
<div class="steps-grid">@foreach([['01','Find your people','Discover local creators by city, style, category and budget.'],['02','Get to know their work','Explore original portfolios, services and transparent starting prices.'],['03','Make it happen','Send a clear brief, agree on the details and bring your idea to life.']] as [$number,$heading,$body])<article>
<span class="step-number">{{ $number }}<x-market-icon name="arrow-right"/>
</span>
<h3>{{ $heading }}</h3>
<p>{{ $body }}</p>
</article>@endforeach</div>
</section>
<section class="container two-sided" id="for-brands">
<div class="brand-benefit">
<span class="eyebrow">FOR BRANDS WITH SOMETHING TO SAY</span>
<h2>Your story.<br>Their creative spark.<br>
<span class="serif-word">A local connection.</span>
</h2>
<p>Work with people who know your city, your audience and the places you call home. From your first brief to your final deliverable, keep every detail in one place.</p>
<a class="button button-dark" href="{{ route('register',['role'=>'brand']) }}">Start a collaboration <x-market-icon name="arrow-up-right"/>
</a>
<div class="benefit-tags">
<span>
<x-market-icon name="check"/>Clear service pricing</span>
<span>
<x-market-icon name="check"/>Direct conversations</span>
</div>
</div>
<div class="creator-cta" id="for-creators">
<div class="large-asterisk">✳</div>
<span class="eyebrow">FOR THE CREATIVELY INDEPENDENT</span>
<h2>Your talent.<br>
<span class="serif-word">A bigger stage.</span>
</h2>
<p>Put your work out there. Set your own services. Connect with brands that see your potential.</p>
<a class="button button-white" href="{{ route('register',['role'=>'creator']) }}">Join as a creator <x-market-icon name="arrow-up-right"/>
</a>
<span class="creator-cta-note">Your city is full of stories. Help tell them.</span>
</div>
</section>
<section class="section container faq-section" id="faq">
<div>
<span class="eyebrow">GOOD QUESTIONS. STRAIGHT ANSWERS.</span>
<h2>A little more<br>
<span class="serif-word">before you start.</span>
</h2>
</div>
<div class="faq-list">@foreach([['Who can join Shoutly?','Influencers, UGC creators, photographers and other original content creators can apply. Local businesses and brands can register to discover creators and publish campaign briefs.'],['Where is Shoutly available?','We’re starting with Yamunanagar, Jagadhri and nearby Haryana cities, with room to grow across India.'],['How do payments work?','You and your collaborator agree on payment terms and coordinate payments directly. Shoutly does not collect payments, provide escrow or guarantee payment.'],['How are creator profiles reviewed?','Creators submit their portfolio, services and publication consent. An administrator reviews profiles before they appear publicly. Social metrics are clearly marked as self-reported unless independently verified.'],['How do I get started as a creator?','Create an account, verify your email, build your profile, add original work and service packages, and submit your profile for review. You can save a draft at any time.']] as [$question,$answer])<details>
<summary>{{ $question }}<span>+</span>
</summary>
<p>{{ $answer }}</p>
</details>@endforeach</div>
</section>
<section class="closing-line container">
<span>Local roots.</span>
<span class="serif-word">Extraordinary reach.</span>
<a href="{{ route('creators') }}" aria-label="Explore creators">
<x-market-icon name="arrow-up-right"/>
</a>
</section>
</x-layouts::marketplace>
