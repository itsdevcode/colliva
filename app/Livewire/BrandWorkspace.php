<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class BrandWorkspace extends Component
{
    public string $companyName = '';

    public string $website = '';

    public string $description = '';

    public string $city = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->role === 'brand', 403);
        $profile = auth()->user()->brandProfile;
        if ($profile) {
            $this->companyName = $profile->company_name;
            $this->website = $profile->website ?? '';
            $this->description = $profile->description ?? '';
            $this->city = (string) $profile->location_id;
        }
    }

    public function save(): void
    {
        abort_unless(auth()->user()->role === 'brand', 403);
        $this->validate(['companyName' => ['required', 'string', 'max:150'], 'website' => ['nullable', 'url:https', 'max:255'], 'description' => ['required', 'string', 'max:3000'], 'city' => ['required', 'exists:locations,id']]);
        auth()->user()->brandProfile()->updateOrCreate(['user_id' => auth()->id()], ['company_name' => $this->companyName, 'website' => $this->website ?: null, 'description' => $this->description, 'location_id' => $this->city]);
        session()->flash('status', 'Business profile saved.');
    }

    public function removeSaved(int $id): void
    {
        abort_unless(auth()->user()->role === 'brand', 403);
        auth()->user()->savedCreators()->detach($id);
    }

    public function render(): View
    {
        return view('livewire.brand-workspace', ['locations' => Location::orderBy('name')->get(), 'savedCreators' => auth()->user()->savedCreators()->publiclyVisible()->with(['category', 'location'])->get()])->title('Brand workspace');
    }
}
