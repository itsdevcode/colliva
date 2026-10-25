<?php

namespace App\Livewire;

use App\Models\SupportArticle;
use App\Models\SupportCategory;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class SupportCenter extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $category = '';

    public function clearSearch(): void
    {
        $this->reset('search');
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category');
    }

    public function render(): View
    {
        $articles = SupportArticle::published()->with('category');
        if (trim($this->search) !== '') {
            $term = '%'.mb_substr(trim($this->search), 0, 150).'%';
            $articles->where(fn ($query) => $query->whereLike('title', $term)->orWhereLike('summary', $term)->orWhereLike('content', $term)->orWhereHas('category', fn ($category) => $category->whereLike('name', $term)->orWhereLike('description', $term)));
        }
        if ($this->category !== '') {
            $articles->whereHas('category', fn ($query) => $query->where('slug', $this->category));
        }

        $articles = $articles->orderBy('sort_order')->orderBy('id')->get();

        return view('livewire.support-center', [
            'categories' => SupportCategory::withCount(['articles' => fn ($query) => $query->published()])->orderBy('sort_order')->orderBy('id')->get(),
            'articles' => $articles,
            'articleGroups' => $articles->groupBy('support_category_id'),
        ])->title('Support Center');
    }
}
