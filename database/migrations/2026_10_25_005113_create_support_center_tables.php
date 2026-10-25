<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('support_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('content');
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
            $table->index(['support_category_id', 'sort_order']);
        });
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 40)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('email');
            $table->string('user_type', 20);
            $table->string('category', 50);
            $table->string('subject', 160);
            $table->text('description');
            $table->string('related_reference', 120)->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamp('last_activity_at');
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'last_activity_at']);
            $table->index(['status', 'last_activity_at']);
        });
        Schema::create('support_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->boolean('internal')->default(false);
            $table->boolean('staff')->default(false);
            $table->timestamps();
            $table->index(['support_ticket_id', 'internal', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_articles');
        Schema::dropIfExists('support_categories');
    }
};
