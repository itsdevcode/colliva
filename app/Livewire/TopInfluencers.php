<?php

namespace App\Livewire;

use App\Actions\RankCreators;
use App\Models\Category;
use App\Models\Location;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.marketplace')]
class TopInfluencers extends Component
{
    use WithPagination;

    #[Url]
    public string $platform = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $sort = 'recommended';

    #[Locked]
    public ?string $location = null;

    #[Locked]
    public ?string $locationName = null;

    public function mount(?string $topic = null): void
    {
        $this->normalizeSort();
        if ($topic === null) {
            return;
        }
        $location = Location::where('slug', $topic)->first();
        if ($location) {
            $this->location = $location->slug;
            $this->locationName = $location->name;
        } else {
            $this->category = Category::where('slug', $topic)->firstOrFail()->slug;
        }
    }

    public function updated(string $property): void
    {
        $this->normalizeSort();
        $this->resetPage();
    }

    public function resetControls(): void
    {
        $this->reset('platform', 'category', 'sort');
        $this->resetPage();
    }

    private function normalizeSort(): void
    {
        if (! in_array($this->sort, ['recommended', 'followers', 'active', 'price-asc'], true)) {
            $this->sort = 'recommended';
        }
    }

    public function render(RankCreators $ranking): View
    {
        $sort = in_array($this->sort, ['recommended', 'followers', 'active', 'price-asc'], true) ? $this->sort : 'recommended';

        return view('livewire.top-influencers', [
            'creators' => $ranking->query($this->platform, $this->category, $this->location, $sort)->paginate(12),
            'categories' => Category::orderBy('name')->get(),
            'activeSort' => $sort,
        ])->title('Top Influencers — Creator Leaderboard');
    }
}
