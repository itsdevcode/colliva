<div class="container section support-form-page">
<nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('support') }}">Support Center</a><span aria-hidden="true">/</span><span>Contact support</span></nav>
@if($reference !== '')
<section class="support-confirmation" role="status"><span class="support-topic-icon"><x-market-icon name="check" /></span><span class="eyebrow">Request received</span><h1>We have your request.</h1><p>Your ticket reference</p><strong class="ticket-reference">{{ $reference }}</strong><p>Keep this reference for any follow-up. {{ $emailQueued ? 'An email notification has been queued or sent.' : 'Your request is saved. No email notification was queued.' }}</p>@auth<x-colliva-button :href="route('support.ticket', $reference)">View your ticket</x-colliva-button>@else<p>For follow-ups, submit another request and include this reference. Guest tickets are not visible in account ticket history.</p>@endauth<a class="text-link" href="{{ route('support') }}">Back to Support Center</a></section>
@else
<div class="section-heading"><div><span class="eyebrow">A conversation starts here</span><h1>Tell us how we can help.</h1><p>Share the details below. Please leave out passwords, payment card numbers and other sensitive information.</p></div></div>
<x-workspace-errors />
<form wire:submit="submit" class="support-ticket-form">
    <div class="support-form-grid">
        <label>Full name<input wire:model="name" autocomplete="name" maxlength="100" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
        <label>Email<input type="email" wire:model="email" autocomplete="email" maxlength="255" required @readonly(auth()->check())>@auth<small>Replies use your account email.</small>@endauth @error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
        <label>I’m a<select wire:model="userType"><option value="creator">Creator</option><option value="brand">Brand</option><option value="customer">Customer</option><option value="other">Other</option></select>@error('userType')<span class="field-error">{{ $message }}</span>@enderror</label>
        <label>Issue category<select wire:model="category">@foreach(\App\Models\SupportTicket::CATEGORIES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>@error('category')<span class="field-error">{{ $message }}</span>@enderror</label>
    </div>
    <label>Subject<input wire:model="subject" minlength="5" maxlength="160" required>@error('subject')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>What’s happening?<textarea wire:model="description" rows="7" minlength="20" maxlength="10000" required></textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Related enquiry, campaign or ticket reference <span>(optional)</span><input wire:model="relatedReference" maxlength="120">@error('relatedReference')<span class="field-error">{{ $message }}</span>@enderror</label>
    <div class="support-honeypot" aria-hidden="true"><label>Leave this field empty<input wire:model="website" tabindex="-1" autocomplete="off"></label></div>
    <p class="support-form-note">Attachments are not accepted. Describe the issue and include a reference where possible.</p>
    <x-colliva-button type="submit" wire:loading.attr="disabled"><span wire:loading.remove wire:target="submit">Send support request</span><span wire:loading wire:target="submit">Saving your request…</span><x-market-icon name="arrow-right" /></x-colliva-button>
</form>
@endif
</div>
