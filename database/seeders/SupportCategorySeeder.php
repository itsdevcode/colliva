<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SupportArticleSeeder::class);
    }
}
