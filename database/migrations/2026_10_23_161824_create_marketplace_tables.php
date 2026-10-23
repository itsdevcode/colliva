<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['creator', 'brand', 'admin'])->default('creator')->index();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('state')->default('Haryana');
            $table->string('country', 2)->default('IN');
            $table->timestamps();
        });
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('moderation_note')->nullable();
            $table->timestamp('publication_consented_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('available')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->index(['status', 'location_id', 'category_id']);
            $table->timestamps();
        });
        Schema::create('brand_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('creator_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unique(['creator_profile_id', 'category_id']);
            $table->timestamps();
        });
        Schema::create('creator_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->unique(['creator_profile_id', 'language_id']);
            $table->timestamps();
        });
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'youtube', 'ugc', 'other']);
            $table->string('url');
            $table->unsignedBigInteger('followers')->nullable();
            $table->unsignedBigInteger('average_views')->nullable();
            $table->timestamp('metrics_reported_at')->nullable();
            $table->timestamp('metrics_verified_at')->nullable();
            $table->unique(['creator_profile_id', 'platform']);
            $table->timestamps();
        });
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamp('rights_confirmed_at');
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('price_minor');
            $table->text('deliverables');
            $table->unsignedSmallInteger('turnaround_days');
            $table->string('revision_policy');
            $table->index('price_minor');
            $table->timestamps();
        });
        Schema::create('saved_creators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'creator_profile_id']);
            $table->timestamps();
        });
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('deliverables');
            $table->text('requirements');
            $table->unsignedBigInteger('budget_minor');
            $table->date('application_deadline');
            $table->date('delivery_deadline');
            $table->enum('status', ['draft', 'pending', 'published', 'closed', 'cancelled', 'completed'])->default('draft');
            $table->index(['status', 'application_deadline']);
            $table->timestamps();
        });
        Schema::create('campaign_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->unique(['campaign_id', 'location_id']);
            $table->timestamps();
        });
        Schema::create('campaign_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'youtube', 'ugc', 'other']);
            $table->unique(['campaign_id', 'platform']);
            $table->timestamps();
        });
        Schema::create('campaign_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->text('proposal');
            $table->unsignedBigInteger('quote_minor');
            $table->enum('status', ['submitted', 'shortlisted', 'accepted', 'rejected', 'withdrawn'])->default('submitted');
            $table->unique(['campaign_id', 'creator_profile_id']);
            $table->timestamps();
        });
        Schema::create('collaboration_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_application_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('scope');
            $table->unsignedBigInteger('price_minor');
            $table->date('delivery_deadline');
            $table->enum('status', ['pending', 'changes_requested', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->text('creator_response')->nullable();
            $table->timestamp('creator_confirmed_at')->nullable();
            $table->timestamp('brand_confirmed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('collaborations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaboration_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'submitted', 'revision_requested', 'completed'])->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('collaboration_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaboration_id')->constrained()->cascadeOnDelete();
            $table->text('notes');
            $table->string('url');
            $table->timestamps();
        });
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_profile_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->text('note')->nullable();
            $table->timestamps();
        });
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('content_reports');
        Schema::dropIfExists('collaboration_deliverables');
        Schema::dropIfExists('collaborations');
        Schema::dropIfExists('collaboration_requests');
        Schema::dropIfExists('campaign_applications');
        Schema::dropIfExists('campaign_platforms');
        Schema::dropIfExists('campaign_location');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('saved_creators');
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('creator_language');
        Schema::dropIfExists('creator_category');
        Schema::dropIfExists('brand_profiles');
        Schema::dropIfExists('creator_profiles');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('categories');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};
