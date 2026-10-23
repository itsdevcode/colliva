<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, list<list<string>>> */
    private array $indexes = [
        'portfolio_items' => [['creator_profile_id', 'hidden']],
        'service_packages' => [['creator_profile_id', 'price_minor']],
        'campaigns' => [['brand_profile_id', 'status']],
        'campaign_applications' => [['creator_profile_id', 'status']],
        'collaboration_requests' => [['brand_profile_id', 'status'], ['creator_profile_id', 'status']],
        'collaboration_deliverables' => [['collaboration_id']],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $name => $indexes) {
            Schema::table($name, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $columns) {
                    $table->index($columns);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $name => $indexes) {
            Schema::table($name, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $columns) {
                    $table->dropIndex($columns);
                }
            });
        }
    }
};
