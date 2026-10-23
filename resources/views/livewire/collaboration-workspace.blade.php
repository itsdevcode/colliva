<div class="container section workspace-page">
<span class="eyebrow">FROM FIRST HELLO TO FINAL DELIVERY</span>
<div class="section-heading">
<div>
<h1>Good things, <span class="serif-word">made together.</span>
</h1>
<p>Your briefs, agreements and creative work. All in one place.</p>
</div>
</div>
<x-workspace-errors/>
<div class="notice notice-neutral">Payments are coordinated directly between you and your collaborator. Colliva does not collect payments, secure funds or provide escrow.</div>
@if(auth()->user()->role === 'brand' && $creator)<section class="workspace-card">
<h2>Tell them what you have in mind.</h2>
<form wire:submit="send">
<label>Creator username<input wire:model="creator" required>
</label>
<label>Project title<input wire:model="title" required maxlength="150">
</label>
<label>Scope, deliverables & payment terms<textarea wire:model="scope" required minlength="20" placeholder="Describe what you want to make, the deliverables, usage rights and how you’ll coordinate payment.">
</textarea>
</label>
<div class="form-grid">
<label>Agreed offer (₹)<input type="number" min="1" step=".01" wire:model="price" required>
</label>
<label>Delivery deadline<input type="date" wire:model="deadline" required>
</label>
</div>
<button class="button button-purple" wire:loading.attr="disabled">Send brief & confirm terms <x-market-icon name="arrow-up-right" /></button>
<small class="form-help">Sending a brief confirms your proposed terms. Work begins only after the creator accepts.</small>
</form>
</section>@endif
@forelse($offers as $offer)<article class="workspace-card" wire:key="offer-{{ $offer->id }}">
<span class="status-badge">{{ $offer->collaboration?->status ?? $offer->status }}</span>
<h2 style="margin-top:15px">{{ $offer->title }}</h2>
<span class="eyebrow">{{ $offer->brandProfile->company_name }} × {{ $offer->creatorProfile->display_name }}</span>
<p>{{ $offer->scope }}</p>
<div class="campaign-meta">
<span>₹{{ number_format($offer->price_minor/100,2) }}</span>
<span>Deliver by {{ $offer->delivery_deadline->format('d M Y') }}</span>
<span>Payment: coordinated directly</span>
</div>@if($offer->creator_response)<div class="notice" style="background:#f6f3ec">Creator response: {{ $offer->creator_response }}</div>@endif
@if(auth()->user()->role === 'creator' && $offer->status === 'pending')<label>Your response (required when requesting changes)<textarea wire:model="responses.{{ $offer->id }}" maxlength="2000">
</textarea>
</label>
<div class="campaign-actions">
<button class="button button-purple" wire:click="respond({{ $offer->id }},'accepted')">Accept scope & terms</button>
<button class="button button-outline" wire:click="respond({{ $offer->id }},'changes_requested')">Propose changes</button>
<button class="text-link" wire:click="respond({{ $offer->id }},'rejected')">Decline</button>
</div>@endif
@if(auth()->user()->role === 'brand' && $offer->status === 'changes_requested')<form wire:submit="revise({{ $offer->id }})" class="offer-form">
<label>Revised scope<textarea wire:model="revisedScopes.{{ $offer->id }}" required>
</textarea>
</label>
<div class="form-grid">
<label>Revised price (₹)<input wire:model="revisedPrices.{{ $offer->id }}" type="number" min="1" step=".01" required>
</label>
<label>Revised deadline<input wire:model="revisedDeadlines.{{ $offer->id }}" type="date" required>
</label>
</div>
<button class="button button-purple">Send revised brief</button>
</form>@endif
@if(auth()->user()->role === 'brand' && in_array($offer->status,['pending','changes_requested']))<button class="text-link" style="margin-top:20px" wire:click="respond({{ $offer->id }},'cancelled')">Cancel request</button>@endif
@if($offer->collaboration)<div class="collaboration-detail">
<h3>Agreed collaboration</h3>
<p>Both parties confirmed the scope and commercial terms.</p>@foreach($offer->collaboration->deliverables as $deliverable)<div class="item-row" wire:key="deliverable-{{ $deliverable->id }}">
<div>
<a class="text-link" href="{{ $deliverable->url }}" target="_blank" rel="noopener noreferrer">View submitted work <x-market-icon name="arrow-up-right" /></a>
<p>{{ $deliverable->notes }}</p>
<small>{{ $deliverable->created_at->format('d M Y, H:i') }}</small>
</div>
</div>@endforeach
@if(auth()->user()->role === 'creator' && in_array($offer->collaboration->status,['active','revision_requested']))<form wire:submit="submit({{ $offer->collaboration->id }})" class="offer-form">
<label>Link to deliverables<input wire:model="urls.{{ $offer->collaboration->id }}" type="url" placeholder="https://" required>
</label>
<label>Delivery notes<textarea wire:model="notes.{{ $offer->collaboration->id }}" required>
</textarea>
</label>
<button class="button button-purple" wire:loading.attr="disabled">Submit deliverables</button>
</form>@endif
@if(auth()->user()->role === 'brand' && $offer->collaboration->status === 'submitted')<div class="campaign-actions">
<button class="button button-purple" wire:click="review({{ $offer->collaboration->id }},'completed')">Approve & complete</button>
<button class="button button-outline" wire:click="review({{ $offer->collaboration->id }},'revision_requested')">Request revision</button>
</div>@endif</div>@endif</article>@empty<div class="empty-state">
<div class="empty-symbol">✳</div>
<h2>A great collaboration starts with hello.</h2>
<p>{{ auth()->user()->role === 'brand' ? 'Find a creator you connect with and send them a brief from their profile.' : 'Your collaboration requests will appear here. Keep making work that feels like you.' }}</p>
<a class="button button-outline" href="{{ route('creators') }}">Explore creators <x-market-icon name="arrow-up-right" /></a>
</div>@endforelse
<section class="workspace-card" style="margin-top:30px">
<div class="section-heading">
<h2>Your notifications.</h2>
<button class="text-link" wire:click="markRead">Mark all read</button>
</div>@forelse($notifications as $notification)<div class="item-row" wire:key="notification-{{ $notification->id }}">
<div>{{ $notification->data['message'] ?? 'Workspace update' }}<small>{{ $notification->created_at->diffForHumans() }} {{ $notification->read_at ? '' : '· unread' }}</small>
</div>
</div>@empty<p>You’re all caught up. Updates about your work will appear here.</p>@endforelse</section>
<div wire:loading.delay class="form-help" role="status">Updating your collaboration…</div>
</div>
