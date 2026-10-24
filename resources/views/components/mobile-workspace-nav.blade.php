@auth
    @if(auth()->user()->hasVerifiedEmail() && in_array(auth()->user()->role, ['creator', 'brand', 'customer'], true) && request()->routeIs('dashboard', '*.dashboard', 'creator.studio', 'creator.onboarding', 'brand.workspace', 'brand.onboarding', 'creators', 'creators.location', 'campaigns', 'collaborations', '*.edit'))
        @php
            $role = auth()->user()->role;
            $studioRoute = request()->routeIs('creator.studio', 'creator.onboarding');
            $items = match ($role) {
                'creator' => [
                    ['Overview', 'globe', route('creator.dashboard'), request()->routeIs('creator.dashboard')],
                    ['Portfolio', 'camera', route('creator.studio', ['step' => 4]), $studioRoute && (int) request('step', 1) === 4],
                    ['Opportunities', 'briefcase', route('campaigns'), request()->routeIs('campaigns')],
                    ['Requests', 'heart', route('collaborations'), request()->routeIs('collaborations')],
                    ['Profile', 'shield', route('creator.studio'), $studioRoute && (int) request('step', 1) !== 4],
                ],
                'brand' => [
                    ['Discover', 'search', route('creators'), request()->routeIs('creators', 'creators.location')],
                    ['Saved', 'heart', route('brand.workspace').'#saved-creators', request()->routeIs('brand.workspace', 'brand.onboarding')],
                    ['Campaigns', 'briefcase', route('campaigns'), request()->routeIs('campaigns')],
                    ['Requests', 'sparkles', route('collaborations'), request()->routeIs('collaborations')],
                    ['Account', 'shield', route('profile.edit'), request()->routeIs('*.edit')],
                ],
                'customer' => [
                    ['Discover', 'search', route('creators'), request()->routeIs('creators', 'creators.location')],
                    ['Saved', 'heart', route('customer.dashboard').'#saved-creators', request()->routeIs('customer.dashboard')],
                    ['Enquiries', 'briefcase', route('collaborations').'#enquiries', request()->routeIs('collaborations')],
                    ['Collaborations', 'sparkles', route('collaborations').'#active-collaborations', false],
                    ['Profile', 'shield', route('profile.edit'), request()->routeIs('*.edit')],
                ],
            };
        @endphp
        <nav x-data="{ currentHash: window.location.hash, studioStep: Number(new URL(window.location.href).searchParams.get('step') || 1) }" @hashchange.window="currentHash = window.location.hash" @studio-step-changed.window="studioStep = $event.detail.step" class="mobile-workspace-nav" aria-label="{{ ucfirst($role) }} quick navigation">
            @foreach($items as [$label, $icon, $href, $active])
                <a href="{{ $href }}"
                    @if($role === 'customer' && request()->routeIs('collaborations') && in_array($label, ['Enquiries', 'Collaborations'], true))
                        :aria-current="{{ $label === 'Collaborations' ? "currentHash === '#active-collaborations'" : "currentHash !== '#active-collaborations'" }} ? 'page' : null"
                    @elseif($role === 'creator' && $studioRoute && in_array($label, ['Portfolio', 'Profile'], true))
                        :aria-current="{{ $label === 'Portfolio' ? 'studioStep === 4' : 'studioStep !== 4' }} ? 'page' : null"
                    @elseif($active) aria-current="page" @endif><x-market-icon :name="$icon" /><span>{{ $label }}</span></a>
            @endforeach
        </nav>
    @endif
@endauth
