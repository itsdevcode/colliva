<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.marketplace')]
class CreatorOnboarding extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $editingPackageId = null;

    #[Locked]
    public ?int $editingPortfolioId = null;

    public int $step = 1;

    public string $displayName = '';

    public string $slug = '';

    public string $bio = '';

    public string $city = '';

    public string $category = '';

    /** @var array<int, int|string> */
    public array $languages = [];

    /** @var array<int, int|string> */
    public array $secondaryCategories = [];

    public bool $available = true;

    public ?TemporaryUploadedFile $photo = null;

    public ?TemporaryUploadedFile $cover = null;

    public ?TemporaryUploadedFile $portfolioImage = null;

    public string $platform = 'instagram';

    public string $socialUrl = '';

    public ?int $followers = null;

    public ?int $averageViews = null;

    public string $portfolioTitle = '';

    public string $portfolioDescription = '';

    public string $videoUrl = '';

    public bool $rightsConfirmed = false;

    public string $packageName = '';

    public string $packageDescription = '';

    public string $packagePrice = '';

    public string $deliverables = '';

    public int $turnaroundDays = 7;

    public string $revisionPolicy = 'One revision included';

    public bool $publicationConsent = false;

    public function mount(): void
    {
        Gate::authorize('create', CreatorProfile::class);
        $profile = auth()->user()->creatorProfile;
        if ($profile) {
            $this->displayName = $profile->display_name;
            $this->slug = $profile->slug;
            $this->bio = $profile->bio ?? '';
            $this->city = (string) $profile->location_id;
            $this->category = (string) $profile->category_id;
            $this->languages = $profile->languages->modelKeys();
            $this->secondaryCategories = $profile->categories->modelKeys();
            $this->available = $profile->available;
        }
    }

    private function profile(): CreatorProfile
    {
        $profile = auth()->user()->creatorProfile()->firstOrFail();
        Gate::authorize('update', $profile);

        return $profile;
    }

    private function markDraft(CreatorProfile $profile): void
    {
        $profile->forceFill(['status' => 'draft', 'moderation_note' => null])->save();
    }

    public function saveProfile(): void
    {
        Gate::authorize('create', CreatorProfile::class);
        $profile = auth()->user()->creatorProfile;
        $this->validate(['displayName' => ['required', 'string', 'max:100'], 'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('creator_profiles', 'slug')->ignore($profile?->id)], 'bio' => ['required', 'string', 'max:2000'], 'city' => ['required', 'exists:locations,id'], 'category' => ['required', 'exists:categories,id'], 'languages' => ['required', 'array', 'max:10'], 'languages.*' => ['integer', 'exists:languages,id'], 'secondaryCategories' => ['array', 'max:8'], 'secondaryCategories.*' => ['integer', 'exists:categories,id'], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']]);
        $this->validate(['cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']]);
        DB::transaction(function () {
            $data = ['display_name' => $this->displayName, 'slug' => $this->slug, 'bio' => $this->bio, 'location_id' => $this->city, 'category_id' => $this->category, 'available' => $this->available];
            if ($this->photo) {
                $data['photo_path'] = $this->photo->store('creator-photos', config('filesystems.media_disk'));
            }
            if ($this->cover) {
                $data['cover_path'] = $this->cover->store('creator-covers', config('filesystems.media_disk'));
            }
            $profile = auth()->user()->creatorProfile()->updateOrCreate(['user_id' => auth()->id()], $data);
            $profile->languages()->sync($this->languages);
            $profile->categories()->sync($this->secondaryCategories);
            $this->markDraft($profile);
        });
        $this->photo = null;
        $this->cover = null;
        $this->step = 2;
        session()->flash('status', 'Profile draft saved.');
    }

    public function saveSocial(): void
    {
        $profile = $this->profile();
        $this->validate(['platform' => ['required', 'in:instagram,youtube,ugc,other'], 'socialUrl' => ['required', 'url:https', 'max:255'], 'followers' => ['nullable', 'integer', 'min:0', 'max:2000000000'], 'averageViews' => ['nullable', 'integer', 'min:0', 'max:2000000000']]);
        $social = $profile->socialAccounts()->updateOrCreate(['platform' => $this->platform], ['url' => $this->socialUrl, 'followers' => $this->followers, 'average_views' => $this->averageViews, 'metrics_reported_at' => now()]);
        $social->forceFill(['metrics_verified_at' => null])->save();
        $this->markDraft($profile);
        $this->reset('socialUrl', 'followers', 'averageViews');
        session()->flash('status', 'Social account saved. Metrics are labelled self-reported.');
    }

    public function addPortfolio(): void
    {
        $profile = $this->profile();
        $item = $this->editingPortfolioId ? $profile->portfolioItems()->findOrFail($this->editingPortfolioId) : null;
        $this->validate(['portfolioTitle' => ['required', 'string', 'max:150'], 'portfolioDescription' => ['nullable', 'string', 'max:2000'], 'portfolioImage' => [Rule::requiredIf(! $item?->image_path && $this->videoUrl === ''), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'], 'videoUrl' => [Rule::requiredIf(! $item?->image_path && ! $this->portfolioImage), 'nullable', 'url:https', 'max:255'], 'rightsConfirmed' => ['accepted']]);
        DB::transaction(function () use ($profile, $item) {
            $data = ['title' => $this->portfolioTitle, 'description' => $this->portfolioDescription, 'image_path' => $this->portfolioImage?->store('portfolio', config('filesystems.media_disk')) ?? $item?->image_path, 'video_url' => $this->videoUrl ?: null, 'rights_confirmed_at' => now()];
            if ($item) {
                $item->update($data);
            } else {
                $profile->portfolioItems()->create($data);
            }
            $this->markDraft($profile);
        });
        $this->cancelPortfolioEdit();
        session()->flash('status', 'Portfolio item saved.');
    }

    public function editPortfolio(int $id): void
    {
        $item = $this->profile()->portfolioItems()->findOrFail($id);
        $this->cancelPortfolioEdit();
        $this->editingPortfolioId = $item->id;
        $this->portfolioTitle = $item->title;
        $this->portfolioDescription = $item->description ?? '';
        $this->videoUrl = $item->video_url ?? '';
        $this->step = 4;
    }

    public function cancelPortfolioEdit(): void
    {
        $this->reset('editingPortfolioId', 'portfolioTitle', 'portfolioDescription', 'portfolioImage', 'videoUrl', 'rightsConfirmed');
        $this->resetValidation();
    }

    public function addPackage(): void
    {
        $profile = $this->profile();
        $this->validate(['packageName' => ['required', 'string', 'max:100'], 'packageDescription' => ['required', 'string', 'max:2000'], 'packagePrice' => ['required', 'regex:/^\d{1,7}(\.\d{1,2})?$/', 'numeric', 'min:1', 'max:10000000'], 'deliverables' => ['required', 'string', 'max:2000'], 'turnaroundDays' => ['required', 'integer', 'min:1', 'max:365'], 'revisionPolicy' => ['required', 'string', 'max:255']]);
        $profile->servicePackages()->updateOrCreate(['id' => $this->editingPackageId], ['name' => $this->packageName, 'description' => $this->packageDescription, 'price_minor' => (int) round((float) $this->packagePrice * 100), 'deliverables' => $this->deliverables, 'turnaround_days' => $this->turnaroundDays, 'revision_policy' => $this->revisionPolicy]);
        $this->markDraft($profile);
        $this->reset('editingPackageId', 'packageName', 'packageDescription', 'packagePrice', 'deliverables');
        session()->flash('status', 'Service package saved.');
    }

    public function deletePortfolio(int $id): void
    {
        $profile = $this->profile();
        $profile->portfolioItems()->findOrFail($id)->delete();
        $this->markDraft($profile);
    }

    public function editPackage(int $id): void
    {
        $package = $this->profile()->servicePackages()->findOrFail($id);
        $this->editingPackageId = $package->id;
        $this->packageName = $package->name;
        $this->packageDescription = $package->description;
        $this->packagePrice = number_format($package->price_minor / 100, 2, '.', '');
        $this->deliverables = $package->deliverables;
        $this->turnaroundDays = $package->turnaround_days;
        $this->revisionPolicy = $package->revision_policy;
        $this->step = 5;
    }

    public function deletePackage(int $id): void
    {
        $profile = $this->profile();
        $profile->servicePackages()->findOrFail($id)->delete();
        $this->markDraft($profile);
    }

    public function deleteSocial(int $id): void
    {
        $profile = $this->profile();
        $profile->socialAccounts()->findOrFail($id)->delete();
        $this->markDraft($profile);
    }

    public function unpublish(): void
    {
        $this->profile()->forceFill(['status' => 'draft', 'publication_consented_at' => null])->save();
        session()->flash('status', 'Your public profile is unpublished.');
    }

    public function submit(): void
    {
        $this->validate(['publicationConsent' => ['accepted']]);
        $profile = $this->profile();
        if (! $profile->photo_path || ! $profile->portfolioItems()->exists() || ! $profile->servicePackages()->exists() || ! $profile->socialAccounts()->exists() || ! $profile->languages()->exists()) {
            $this->addError('publicationConsent', 'Add a photograph, language, social account, portfolio and service package before submitting.');

            return;
        }
        $profile->forceFill(['status' => 'pending', 'publication_consented_at' => now()])->save();
        session()->flash('status', 'Submitted for review. Your profile will be public only after approval.');
    }

    public function render(): View
    {
        return view('livewire.creator-onboarding', ['profile' => auth()->user()->creatorProfile()->with(['socialAccounts', 'portfolioItems', 'servicePackages'])->first(), 'locations' => Location::orderBy('name')->get(), 'categories' => Category::orderBy('name')->get(), 'languageOptions' => Language::orderBy('name')->get()])->title('Creator studio');
    }
}
