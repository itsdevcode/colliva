<x-layouts::marketplace title="Find your place">
    <section class="container role-entry">
        <span class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</span>
        <h1>How would you like<br>to use <span>Colliva?</span></h1>
        <p class="role-intro">Join our creator community, grow your brand, or discover talented creators for your next project.</p>
        <div class="role-cards">
            @foreach([
                ['creator', 'creators', 'camera', 'Create. Connect. Grow.', 'Showcase your work, build your portfolio, and collaborate with brands.'],
                ['brand', 'brands', 'briefcase', 'Find your creative advantage.', 'Discover the right influencers and build successful marketing campaigns.'],
                ['customer', 'customers', 'sparkles', 'Bring your idea to life.', 'Find and hire talented creators for personal projects, events, or individual campaigns.'],
            ] as [$role, $plural, $icon, $tagline, $description])
                <a class="role-card role-card-{{ $role }}" href="{{ route('for-'.$plural) }}">
                    <span class="role-symbol"><x-market-icon :name="$icon" /></span>
                    <span class="eyebrow">{{ $tagline }}</span>
                    <h2>I'm a {{ ucfirst($role) }}</h2>
                    <p>{{ $description }}</p>
                    <span class="role-card-action">Join as {{ ucfirst($role) }} <x-market-icon name="arrow-right" /></span>
                </a>
            @endforeach
        </div>
        <p class="role-entry-login">Already part of Colliva? <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
    </section>
</x-layouts::marketplace>
