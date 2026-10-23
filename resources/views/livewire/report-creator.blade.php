<div class="report-form">@if($sent)<p role="status">Thank you. Your report has been sent for review.</p>@elseif(auth()->user()?->hasVerifiedEmail())<details>
<summary>Report this profile</summary>
<form wire:submit="send">
<label>Tell us what needs reviewing<textarea wire:model="reason" required minlength="20" maxlength="2000">
</textarea>
</label>@error('reason')<p role="alert">{{ $message }}</p>@enderror<button class="button button-outline" wire:loading.attr="disabled">Send report</button>
</form>
</details>@else<a class="text-link" href="{{ route('login') }}">Log in to report this profile</a>@endif</div>
