<x-layouts::auth :title="'Join as '.ucfirst($role)">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="'Your next chapter starts here.'" :description="'Create your '.ucfirst($role).' account and find your place on Colliva.'" />

        <a class="text-sm text-[#6d4aff]" href="{{ route('get-started') }}">← Choose a different account type</a>
        @if($errors->any())<div role="alert" class="rounded-xl bg-red-50 p-4 text-sm text-red-800"><p>Please check your details.</p><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('signup.store', $role) }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="$role === 'brand' ? 'Contact person name' : 'Full name'"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="$role === 'brand' ? 'Work email' : 'Email'"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            @if($role === 'brand')
                <flux:input name="company_name" label="Brand / business name" :value="old('company_name')" required maxlength="150" autocomplete="organization" />
                <flux:input name="business_category" label="Business category" :value="old('business_category')" required maxlength="150" placeholder="e.g. Food & hospitality" />
            @endif
            @if($role !== 'customer')
                <flux:select name="city" label="City" required>
                    <option value="">Choose your city</option>
                    @foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) old('city') === (string) $location->id)>{{ $location->name }}</option>@endforeach
                </flux:select>
            @endif
            @if($role === 'creator')
                <flux:select name="category" label="Primary creator category" required>
                    <option value="">Choose your category</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach
                </flux:select>
            @endif
            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    Create {{ ucfirst($role) }} Account
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
