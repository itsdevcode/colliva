<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.marketplace')]
class CreatorDiscovery extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $city = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $platform = '';

    #[Url]
    public string $language = '';

    #[Url]
    public string $minFollowers = '';

    #[Url]
    public string $maxFollowers = '';

    #[Url]
    public string $budget = '';

    #[Url]
    public bool $available = false;

    #[Url]
    public bool $verified = false;

    #[Url]
    public string $sort = 'recent';

    public function mount(?string $city = null, ?string $category = null): void
    {
        if ($city && ! request()->has('city')) {
            $this->city = $city;
        } if ($category && ! request()->has('category')) {
            $this->category = $category;
        }
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'draftFilters')) {
            return;
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'city', 'category', 'platform', 'language', 'minFollowers', 'maxFollowers', 'budget', 'available', 'verified', 'sort');
        $this->resetPage();
    }

    /** @var array<string, string|bool> */
    public array $draftFilters = [];

    public function openFilters(): void
    {
        foreach (['city', 'category', 'platform', 'language', 'budget', 'minFollowers', 'maxFollowers', 'available', 'verified'] as $property) {
            $this->draftFilters[$property] = $this->{$property};
        }
        $this->resetValidation();
    }

    public function clearDraftFilters(): void
    {
        $this->draftFilters = ['city' => '', 'category' => '', 'platform' => '', 'language' => '', 'budget' => '', 'minFollowers' => '', 'maxFollowers' => '', 'available' => false, 'verified' => false];
        $this->resetValidation();
    }

    public function applyFilters(): void
    {
        $this->validate([
            'draftFilters.city' => ['nullable', 'string', 'exists:locations,slug'],
            'draftFilters.category' => ['nullable', 'string', 'exists:categories,slug'],
            'draftFilters.platform' => ['nullable', 'in:instagram,youtube,ugc,other'],
            'draftFilters.language' => ['nullable', 'string', 'exists:languages,slug'],
            'draftFilters.budget' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'draftFilters.minFollowers' => ['nullable', 'integer', 'min:0', 'max:2000000000'],
            'draftFilters.maxFollowers' => ['nullable', 'integer', 'min:0', 'max:2000000000'],
            'draftFilters.available' => ['required', 'boolean'],
            'draftFilters.verified' => ['required', 'boolean'],
        ]);
        foreach (['city', 'category', 'platform', 'language', 'budget', 'minFollowers', 'maxFollowers'] as $property) {
            $this->{$property} = (string) ($this->draftFilters[$property] ?? '');
        }
        $this->available = (bool) $this->draftFilters['available'];
        $this->verified = (bool) $this->draftFilters['verified'];
        $this->resetPage();
        $this->dispatch('filters-applied');
    }

    public function saveCreator(int $id): void
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['brand', 'customer'], true) && $user->hasVerifiedEmail(), 403);
        $creator = CreatorProfile::publiclyVisible()->findOrFail($id);
        $user->savedCreators()->syncWithoutDetaching([$creator->id]);
        session()->flash('status', 'Creator saved to your workspace.');
    }

    public function render(): View
    {
        $query = CreatorProfile::publiclyVisible()->with(['location', 'category', 'socialAccounts', 'portfolioItems' => fn ($q) => $q->where('hidden', false)])->withMin('servicePackages', 'price_minor');
        if ($this->search !== '') {
            $query->where(fn ($q) => $q->whereLike('display_name', '%'.mb_substr($this->search, 0, 100).'%')->orWhereLike('slug', '%'.mb_substr($this->search, 0, 100).'%'));
        }
        if ($this->city !== '') {
            $query->whereHas('location', fn ($q) => $q->where('slug', $this->city));
        }
        if ($this->category !== '') {
            $query->where(fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category))->orWhereHas('categories', fn ($c) => $c->where('slug', $this->category)));
        }
        if ($this->language !== '') {
            $query->whereHas('languages', fn ($q) => $q->where('slug', $this->language));
        }
        if ($this->platform !== '' || is_numeric($this->minFollowers) || is_numeric($this->maxFollowers)) {
            $query->whereHas('socialAccounts', function ($q) {
                if ($this->platform !== '') {
                    $q->where('platform', $this->platform);
                } if (is_numeric($this->minFollowers)) {
                    $q->where('followers', '>=', max(0, (int) $this->minFollowers));
                } if (is_numeric($this->maxFollowers)) {
                    $q->where('followers', '<=', max(0, (int) $this->maxFollowers));
                }
            });
        }
        if (is_numeric($this->budget)) {
            $query->whereHas('servicePackages', fn ($q) => $q->where('price_minor', '<=', min(100000000, max(0, (int) $this->budget)) * 100));
        }
        if ($this->available) {
            $query->where('available', true);
        } if ($this->verified) {
            $query->whereNotNull('verified_at');
        }
        if (in_array($this->sort, ['price-asc', 'price-desc'], true)) {
            $query->orderBy('service_packages_min_price_minor', $this->sort === 'price-asc' ? 'asc' : 'desc');
        }

        return view('livewire.creator-discovery', ['creators' => $query->latest('id')->paginate(12), 'locations' => Location::orderBy('name')->get(), 'categories' => Category::orderBy('name')->get(), 'languages' => Language::orderBy('name')->get()])->title('Explore creators');
    }
}
