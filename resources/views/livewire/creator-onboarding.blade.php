<div class="container section workspace-page">
<span class="eyebrow">YOUR WORK DESERVES TO BE SEEN</span>
<div class="section-heading">
<div>
<h1>Your <span class="serif-word">creator studio.</span>
</h1>
<p>Build a profile that feels like you. Every change is saved as a private draft.</p>
</div>@if($profile)<span class="status-badge">{{ $profile->status }}</span>@endif</div>
<x-workspace-errors/>@if($profile?->moderation_note)<div class="notice notice-neutral">Review feedback: {{ $profile->moderation_note }}</div>@endif
<nav class="studio-tabs" aria-label="Onboarding steps">@foreach(['Your profile','Social accounts','Categories','Portfolio','Services','Review & submit'] as $label)<button wire:click="$set('step', {{ $loop->iteration }})" @class(['active'=>$step === $loop->iteration]) aria-current="{{ $step === $loop->iteration ? 'step':'false' }}">{{ $loop->iteration }}. {{ $label }}</button>@endforeach</nav>
@if($step === 1 || $step === 3)<section class="workspace-card">
<h2>{{ $step === 1 ? 'Start with your story.' : 'Find your creative lane.' }}</h2>
<form wire:submit="saveProfile">
<div class="form-grid">
<label>Display name<input wire:model="displayName" required maxlength="100" autocomplete="name">
</label>
<label>Public username<input wire:model="slug" required maxlength="100" placeholder="your-creative-name">
<small class="form-help">Lowercase letters, numbers and hyphens.</small>
</label>
</div>
<label>About you<textarea wire:model="bio" required maxlength="2000" placeholder="Your style, your story, the work you love to make.">
</textarea>
</label>
<div class="form-grid">
<label>City<select wire:model="city" required>
<option value="">Choose your city</option>@foreach($locations as $option)<option value="{{ $option->id }}">{{ $option->name }}, {{ $option->state }}</option>@endforeach</select>
</label>
<label>Primary category<select wire:model="category" required>
<option value="">Choose your creative lane</option>@foreach($categories as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach</select>
</label>
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
<label>Profile photograph<input type="file" wire:model="photo" accept="image/jpeg,image/png,image/webp">
<small class="form-help">JPG, PNG or WebP. Up to 4 MB.</small>
</label>
<label>Profile cover (optional)<input type="file" wire:model="cover" accept="image/jpeg,image/png,image/webp"><small class="form-help">JPG, PNG or WebP. Up to 4 MB.</small></label>
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
<label>Platform<select wire:model="platform">
<option value="instagram">Instagram</option>
<option value="youtube">YouTube</option>
<option value="ugc">UGC portfolio</option>
<option value="other">Other</option>
</select>
</label>
<label>Profile or channel URL<input type="url" wire:model="socialUrl" placeholder="https://" required>
</label>
<label>Follower count (optional)<input type="number" min="0" wire:model="followers">
</label>
<label>Average views (optional)<input type="number" min="0" wire:model="averageViews">
</label>
</div>
<button class="button button-purple" wire:loading.attr="disabled">Save social account</button>
</form>@foreach($profile->socialAccounts as $social)<div class="item-row" wire:key="social-{{ $social->id }}">
<span>{{ ucfirst($social->platform) }}<small>{{ $social->url }} · self-reported</small>
</span>
<button class="danger" wire:click="deleteSocial({{ $social->id }})">Remove</button>
</div>@endforeach</section>
@elseif($step === 4)<section class="workspace-card">
<h2>Let your work do the talking.</h2>
<form wire:submit="addPortfolio">
<label>Project title<input wire:model="portfolioTitle" required maxlength="150">
</label>
<label>Project description<textarea wire:model="portfolioDescription" maxlength="2000">
</textarea>
</label>
<div class="form-grid">
<label>Image<input type="file" wire:model="portfolioImage" accept="image/jpeg,image/png,image/webp">
<small class="form-help">Up to 8 MB. No SVG files.</small>
</label>
<label>Or a video link<input type="url" wire:model="videoUrl" placeholder="https://">
</label>
</div>
<label class="checkbox-label">
<input type="checkbox" wire:model="rightsConfirmed" required>I own this work or have permission to display it publicly.</label>
<button class="button button-purple" wire:loading.attr="disabled">{{ $editingPortfolioId ? 'Save portfolio item' : 'Add to portfolio' }}</button>
@if($editingPortfolioId)<button type="button" class="text-link" wire:click="cancelPortfolioEdit">Cancel editing</button><p class="form-help">Your existing image is kept unless you upload a replacement.</p>@endif
</form>@foreach($profile->portfolioItems as $item)<div class="item-row" wire:key="portfolio-{{ $item->id }}">
<span>{{ $item->title }}</span>
<button class="text-link" wire:click="editPortfolio({{ $item->id }})">Edit</button>
<button class="danger" wire:click="deletePortfolio({{ $item->id }})">Remove</button>
</div>@endforeach</section>
@elseif($step === 5)<section class="workspace-card">
<h2>Good work. Clear expectations.</h2>
<form wire:submit="addPackage">
<div class="form-grid">
<label>Service name<input wire:model="packageName" required placeholder="UGC product video">
</label>
<label>Price in INR (₹)<input type="number" min="1" step="0.01" wire:model="packagePrice" required>
</label>
</div>
<label>Description<textarea wire:model="packageDescription" required>
</textarea>
</label>
<label>Deliverables<textarea wire:model="deliverables" required placeholder="One 30-second vertical video, edited with captions.">
</textarea>
</label>
<div class="form-grid">
<label>Turnaround (days)<input type="number" min="1" max="365" wire:model="turnaroundDays" required>
</label>
<label>Revision policy<input wire:model="revisionPolicy" required>
</label>
</div>
<button class="button button-purple" wire:loading.attr="disabled">{{ $editingPackageId ? 'Save service package' : 'Add service package' }}</button>
</form>@foreach($profile->servicePackages as $package)<div class="item-row" wire:key="package-{{ $package->id }}">
<span>{{ $package->name }}<small>₹{{ number_format($package->price_minor/100,2) }} · {{ $package->turnaround_days }} days</small>
</span>
<button class="text-link" wire:click="editPackage({{ $package->id }})">Edit</button>
<button class="danger" wire:click="deletePackage({{ $package->id }})">Remove</button>
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
<button class="text-link" style="margin-top:25px;color:#aa4931" wire:click="unpublish">Unpublish my profile</button>
<p class="form-help" style="margin-top:15px">Editing your profile after approval returns it to a private draft for a fresh review.</p>
</section>@endif
@if($profile)<div class="campaign-actions">@if($step > 1)<button class="button button-outline" wire:click="$set('step',{{ $step-1 }})">← Previous</button>@endif @if($step < 6)<button class="button button-dark" wire:click="$set('step',{{ $step+1 }})">Next step →</button>@endif</div>@endif<div wire:loading.delay class="form-help" role="status">Saving your changes…</div>
</div>
