<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f5f5f7] antialiased dark:bg-zinc-950">
        <div class="auth-shell">
            <aside class="auth-story" aria-label="Colliva community">
                <a class="wordmark" href="{{ route('home') }}">colliva<span>.</span></a>
                <div><span class="eyebrow">A LITTLE ORIGINALITY GOES A LONG WAY</span><h2>Good people.<br>Great possibilities.</h2><p>A place for creative minds, thoughtful brands, and ideas waiting to happen.</p></div>
                <figure><img src="{{ asset('images/creative-studio.webp') }}" alt="A sunlit creative studio with a camera and notebook" width="1200" height="800"><figcaption>Space to create. Room to grow. <span>Illustrative imagery</span></figcaption></figure>
                <a href="{{ route('home') }}">← Back to Colliva</a>
            </aside>
            <div class="auth-panel">
                <a href="{{ route('home') }}" class="auth-mobile-logo flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="text-2xl font-semibold tracking-tight">{{ 'Colliva' }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
