<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, array<string, string>> */
    private array $constraints = [
        'service_packages' => ['positive_price' => 'price_minor > 0', 'positive_turnaround' => 'turnaround_days > 0'],
        'campaigns' => ['positive_budget' => 'budget_minor > 0', 'ordered_deadlines' => 'delivery_deadline >= application_deadline'],
        'campaign_applications' => ['positive_quote' => 'quote_minor > 0'],
        'collaboration_requests' => ['positive_offer' => 'price_minor > 0', 'accepted_terms' => "status <> 'accepted' OR (creator_confirmed_at IS NOT NULL AND brand_confirmed_at IS NOT NULL)"],
        'social_accounts' => ['nonnegative_metrics' => '(followers IS NULL OR followers >= 0) AND (average_views IS NULL OR average_views >= 0)'],
        'portfolio_items' => ['portfolio_media' => 'image_path IS NOT NULL OR video_url IS NOT NULL'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        } foreach ($this->constraints as $table => $checks) {
            foreach ($checks as $name => $expression) {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        } foreach ($this->constraints as $table => $checks) {
            foreach ($checks as $name => $expression) {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$name}");
            }
        }
    }
};
