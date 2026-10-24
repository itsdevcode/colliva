<div class="container section workspace-page">
<span class="eyebrow">YOUR WORK DESERVES TO BE SEEN</span>
<div class="section-heading">
<div>
<h1>Your <span class="serif-word">creator studio.</span>
</h1>
<p>Build a profile that feels like you. Use the Save action in each step to keep your progress as a private draft.</p>
</div>@if($profile)<span class="status-badge">{{ $profile->status }}</span>@endif</div>
<a class="text-link" href="{{ route('creator.dashboard') }}">Go to your dashboard <x-market-icon name="arrow-right" /></a><x-workspace-errors/>@if($profile?->moderation_note)<div class="notice notice-neutral">Review feedback: {{ $profile->moderation_note }}</div>@endif
<div class="form-progress studio-progress" role="status"><span>Step {{ $step }} of 6 · {{ ['Basic details', 'Social accounts', 'Category', 'Portfolio', 'Services', 'Review'][$step - 1] ?? 'Basic details' }}</span><progress max="6" value="{{ $step }}">{{ $step }} of 6</progress></div>
<label class="studio-mobile-step">Go to a step<select wire:model.live="step">@foreach(['Basic details','Social accounts','Category','Portfolio','Services','Review'] as $label)<option value="{{ $loop->iteration }}">{{ $loop->iteration }}. {{ $label }}</option>@endforeach</select></label>
<nav class="studio-tabs" aria-label="Onboarding steps">@foreach(['Your profile','Social accounts','Categories','Portfolio','Services','Review & submit'] as $label)<button wire:click="$set('step', {{ $loop->iteration }})" @class(['active'=>$step === $loop->iteration]) aria-current="{{ $step === $loop->iteration ? 'step':'false' }}">{{ $loop->iteration }}. {{ $label }}</button>@endforeach</nav>
@if($step === 1 || $step === 3)<section class="workspace-card">
<h2>{{ $step === 1 ? 'Start with your story.' : 'Find your creative lane.' }}</h2>
<form wire:submit="saveProfile">
<div class="form-grid">
<label>Display name<input @error('displayName') aria-invalid="true" aria-describedby="error-displayName" @enderror wire:model="displayName" required maxlength="100" autocomplete="name">
<x-field-error name="displayName" /></label>
<label>Public username<input @error('slug') aria-invalid="true" aria-describedby="error-slug" @enderror wire:model="slug" required maxlength="100" placeholder="your-creative-name">
<small class="form-help">Lowercase letters, numbers and hyphens.</small>
<x-field-error name="slug" /></label>
</div>
<label>About you<textarea @error('bio') aria-invalid="true" aria-describedby="error-bio" @enderror wire:model="bio" required maxlength="2000" placeholder="Your style, your story, the work you love to make.">
</textarea>
<x-field-error name="bio" /></label>
<div class="form-grid">
<label>City<select @error('city') aria-invalid="true" aria-describedby="error-city" @enderror wire:model="city" required>
<option value="">Choose your city</option>@foreach($locations as $option)<option value="{{ $option->id }}">{{ $option->name }}, {{ $option->state }}</option>@endforeach</select>
<x-field-error name="city" /></label>
<label>Primary category<select @error('category') aria-invalid="true" aria-describedby="error-category" @enderror wire:model="category" required>
<option value="">Choose your creative lane</option>@foreach($categories as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach</select>
<x-field-error name="category" /></label>
</div>
<fieldset>
<legend>Languages you create in</legend>
<div class="checkbox-group">@foreach($languageOptions as $option)<label class="checkbox-label">
<input type="checkbox" wire:model="languages" value="{{ $option->id }}">{{ $option->name }}</label>@endforeach</div>
</fieldset>
<fieldset>
<legend>Other categories (optional)</legend>
<div class="checkbox-group">@foreach($categories as $option)<label class="checkbox-label">
<input type="checkbox" wire:model="secondaryCategories" value="{{ $option->id }}">{{ $option->name }}</label>@endforeach</div>
</fieldset>
<label>Profile photograph<input @error('photo') aria-invalid="true" aria-describedby="error-photo" @enderror type="file" wire:model="photo" accept="image/jpeg,image/png,image/webp">
<small class="form-help">JPG, PNG or WebP. Up to 4 MB.</small>
<x-field-error name="photo" /></label>
<label>Profile cover (optional)<input @error('cover') aria-invalid="true" aria-describedby="error-cover" @enderror type="file" wire:model="cover" accept="image/jpeg,image/png,image/webp"><small class="form-help">JPG, PNG or WebP. Up to 4 MB.</small><x-field-error name="cover" /></label>
<label class="checkbox-label">
<input type="checkbox" wire:model="available">I’m available for collaborations</label>
<button class="button button-purple" wire:loading.attr="disabled">Save draft & continue <x-market-icon name="arrow-right"/>
</button>
</form>
</section>
@elseif(!$profile)<div class="empty-state">
<h2>Start with your profile.</h2>
<p>Save your basic details before adding your creative work.</p>
<button class="button button-purple" wire:click="$set('step',1)">Set up your profile</button>
</div>
@elseif($step === 2)<section class="workspace-card">
<h2>Your corners of the internet.</h2>
<p style="margin-bottom:20px">Metrics are self-reported and displayed with their reporting date.</p>
<form wire:submit="saveSocial">
<div class="form-grid">
<label>Platform<select @error('platform') aria-invalid="true" aria-describedby="error-platform" @enderror wire:model="platform">
<option value="instagram">Instagram</option>
<option value="youtube">YouTube</option>
<option value="ugc">UGC portfolio</option>
<option value="other">Other</option>
</select>
<x-field-error name="platform" /></label>
<label>Profile or channel URL<input @error('socialUrl') aria-invalid="true" aria-describedby="error-socialUrl" @enderror type="url" wire:model="socialUrl" placeholder="https://" required>
<x-field-error name="socialUrl" /></label>
<label>Follower count (optional)<input inputmode="decimal" @error('followers') aria-invalid="true" aria-describedby="error-followers" @enderror type="number" min="0" wire:model="followers">
<x-field-error name="followers" /></label>
<label>Average views (optional)<input inputmode="decimal" @error('averageViews') aria-invalid="true" aria-describedby="error-averageViews" @enderror type="number" min="0" wire:model="averageViews">
<x-field-error name="averageViews" /></label>
</div>
<button class="button button-purple" wire:loading.attr="disabled">Save social account</button>
</form>@foreach($profile->socialAccounts as $social)<div class="item-row" wire:key="social-{{ $social->id }}">
<span>{{ ucfirst($social->platform) }}<small>{{ $social->url }} · self-reported</small>
</span>
<button class="danger" wire:confirm="Remove this social account from your profile?" wire:click="deleteSocial({{ $social->id }})">Remove</button>
</div>@endforeach</section>
@elseif($step === 4)<section class="workspace-card">
<h2>Let your work do the talking.</h2>
<form wire:submit="addPortfolio">
<label>Project title<input @error('portfolioTitle') aria-invalid="true" aria-describedby="error-portfolioTitle" @enderror wire:model="portfolioTitle" required maxlength="150">
<x-field-error name="portfolioTitle" /></label>
<label>Project description<textarea @error('portfolioDescription') aria-invalid="true" aria-describedby="error-portfolioDescription" @enderror wire:model="portfolioDescription" maxlength="2000">
</textarea>
<x-field-error name="portfolioDescription" /></label>
<div class="form-grid">
<label>Image<input @error('portfolioImage') aria-invalid="true" aria-describedby="error-portfolioImage" @enderror type="file" wire:model="portfolioImage" accept="image/jpeg,image/png,image/webp">
<small class="form-help">Up to 8 MB. No SVG files.</small>
<x-field-error name="portfolioImage" /></label>
<label>Or a video link<input @error('videoUrl') aria-invalid="true" aria-describedby="error-videoUrl" @enderror type="url" wire:model="videoUrl" placeholder="https://">
<x-field-error name="videoUrl" /></label>
</div>
<label class="checkbox-label">
<input type="checkbox" wire:model="rightsConfirmed" required>I own this work or have permission to display it publicly.</label>
<button class="button button-purple" wire:loading.attr="disabled">{{ $editingPortfolioId ? 'Save portfolio item' : 'Add to portfolio' }}</button>
@if($editingPortfolioId)<button type="button" class="text-link" wire:click="cancelPortfolioEdit">Cancel editing</button><p class="form-help">Your existing image is kept unless you upload a replacement.</p>@endif
</form>@foreach($profile->portfolioItems as $item)<div class="item-row" wire:key="portfolio-{{ $item->id }}">
<span>{{ $item->title }}</span>
<button class="text-link" wire:click="editPortfolio({{ $item->id }})">Edit</button>
<button class="danger" wire:confirm="Permanently remove this portfolio item?" wire:click="deletePortfolio({{ $item->id }})">Remove</button>
</div>@endforeach</section>
@elseif($step === 5)<section class="workspace-card">
<h2>Good work. Clear expectations.</h2>
<form wire:submit="addPackage">
<div class="form-grid">
<label>Service name<input @error('packageName') aria-invalid="true" aria-describedby="error-packageName" @enderror wire:model="packageName" required placeholder="UGC product video">
<x-field-error name="packageName" /></label>
<label>Price in INR (₹)<input inputmode="decimal" @error('packagePrice') aria-invalid="true" aria-describedby="error-packagePrice" @enderror type="number" min="1" step="0.01" wire:model="packagePrice" required>
<x-field-error name="packagePrice" /></label>
</div>
<label>Description<textarea @error('packageDescription') aria-invalid="true" aria-describedby="error-packageDescription" @enderror wire:model="packageDescription" required>
</textarea>
<x-field-error name="packageDescription" /></label>
<label>Deliverables<textarea @error('deliverables') aria-invalid="true" aria-describedby="error-deliverables" @enderror wire:model="deliverables" required placeholder="One 30-second vertical video, edited with captions.">
</textarea>
<x-field-error name="deliverables" /></label>
<div class="form-grid">
<label>Turnaround (days)<input inputmode="decimal" @error('turnaroundDays') aria-invalid="true" aria-describedby="error-turnaroundDays" @enderror type="number" min="1" max="365" wire:model="turnaroundDays" required>
<x-field-error name="turnaroundDays" /></label>
<label>Revision policy<input @error('revisionPolicy') aria-invalid="true" aria-describedby="error-revisionPolicy" @enderror wire:model="revisionPolicy" required>
<x-field-error name="revisionPolicy" /></label>
</div>
<button class="button button-purple" wire:loading.attr="disabled">{{ $editingPackageId ? 'Save service package' : 'Add service package' }}</button>
</form>@foreach($profile->servicePackages as $package)<div class="item-row" wire:key="package-{{ $package->id }}">
<span>{{ $package->name }}<small>₹{{ number_format($package->price_minor/100,2) }} · {{ $package->turnaround_days }} days</small>
</span>
<button class="text-link" wire:click="editPackage({{ $package->id }})">Edit</button>
<button class="danger" wire:confirm="Permanently remove this service package?" wire:click="deletePackage({{ $package->id }})">Remove</button>
</div>@endforeach</section>
@else<section class="workspace-card">
<span class="eyebrow">YOUR PROFILE AT A GLANCE</span>
<h2>{{ $profile->display_name }}</h2>@if($profile->photo_path)<img src="{{ Storage::disk(config('filesystems.media_disk'))->url($profile->photo_path) }}" alt="Your profile photograph" width="100" height="100" class="profile-avatar">@endif<p>{{ $profile->bio }}</p>
<div class="campaign-meta">
<span>{{ $profile->socialAccounts->count() }} social accounts</span>
<span>{{ $profile->portfolioItems->count() }} portfolio items</span>
<span>{{ $profile->servicePackages->count() }} packages</span>
</div>
<div class="portfolio-grid">@foreach($profile->portfolioItems as $item)<div>@if($item->image_path)<img src="{{ Storage::disk(config('filesystems.media_disk'))->url($item->image_path) }}" alt="{{ $item->title }}" loading="lazy">@endif<h3>{{ $item->title }}</h3>
<p>{{ $item->description }}</p>
</div>@endforeach</div>@foreach($profile->servicePackages as $package)<div class="item-row">
<span>{{ $package->name }}</span>
<strong>₹{{ number_format($package->price_minor/100,2) }}</strong>
</div>@endforeach<form wire:submit="submit" style="margin-top:25px">
<label class="checkbox-label">
<input type="checkbox" wire:model="publicationConsent" required>I consent to publishing this profile, portfolio, social links and service pricing after approval.</label>
<button class="button button-purple" wire:loading.attr="disabled">Submit for review <x-market-icon name="arrow-up-right"/>
</button>
</form>
<button class="text-link" style="margin-top:25px;color:#aa4931" wire:confirm="Hide your profile from public discovery? Your saved work will be kept." wire:click="unpublish">Unpublish my profile</button>
<p class="form-help" style="margin-top:15px">Editing your profile after approval returns it to a private draft for a fresh review.</p>
</section>@endif
@if($profile)<div class="campaign-actions">@if($step > 1)<button class="button button-outline" wire:click="$set('step',{{ $step-1 }})">← Previous</button>@endif @if($step < 6)<button class="button button-dark" wire:click="$set('step',{{ $step+1 }})">Next step →</button>@endif</div>@endif<div wire:loading.delay class="form-help" role="status">Saving your changes…</div>
</div>
