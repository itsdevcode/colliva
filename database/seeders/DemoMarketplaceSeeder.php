<?php

namespace Database\Seeders;

use App\Models\BrandProfile;
use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Demo data is restricted to local and testing environments.');
        }
        $this->call(MarketplaceSeeder::class);
        if (User::where('email', 'creator@demo.example')->exists()) {
            return;
        }
        $creator = User::factory()->create(['name' => 'Demo Creator — Fictional', 'email' => 'creator@demo.example']);
        $profile = CreatorProfile::factory()->create(['user_id' => $creator->id, 'display_name' => 'Demo Creator — Fictional', 'slug' => 'demo-creator-fictional', 'location_id' => Location::where('slug', 'yamunanagar')->firstOrFail()->id, 'category_id' => Category::where('slug', 'food-restaurants')->firstOrFail()->id, 'is_demo' => true, 'bio' => 'Fictional local-development profile. Not a real registered creator.', 'photo_path' => null]);
        ServicePackage::factory()->create(['creator_profile_id' => $profile->id]);
        $profile->languages()->attach(Language::where('slug', 'hindi')->firstOrFail());
        $brand = User::factory()->create(['role' => 'brand', 'name' => 'Demo Brand — Fictional', 'email' => 'brand@demo.example']);
        BrandProfile::factory()->create(['user_id' => $brand->id, 'company_name' => 'Demo Brand — Fictional']);
    }
}
