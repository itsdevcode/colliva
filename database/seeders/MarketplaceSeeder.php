<?php
namespace Database\Seeders;
use App\Models\{Category, Language, Location};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class MarketplaceSeeder extends Seeder
{
 public function run(): void {
 foreach (['Food & Restaurants', 'Fashion', 'Beauty', 'Lifestyle', 'Fitness', 'Travel', 'Technology', 'Gaming', 'UGC Creators'] as $name) { Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]); }
 foreach (['Yamunanagar', 'Jagadhri', 'Ambala', 'Kurukshetra', 'Karnal', 'Panchkula', 'Panipat'] as $name) { Location::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'state' => 'Haryana', 'country' => 'IN']); }
 foreach (['Hindi', 'English', 'Punjabi', 'Haryanvi'] as $name) { Language::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]); }
 }
}
