<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Active'); // Active, Paused
            $table->timestamps();
        });

        Schema::create('platform_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('platform'); // reddit, twitter, linkedin
            $table->string('username');
            $table->text('session_cookies')->nullable();
            $table->string('status')->default('Active'); // Active, Rate Limited, Verification Required
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('keyword');
            $table->string('status')->default('Active'); // Active, Paused
            $table->timestamp('last_scraped_at')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('platform');
            $table->string('external_id');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('author');
            $table->string('url')->nullable();
            $table->string('status')->default('Pending'); // Pending, Qualified, Rejected
            $table->timestamp('scraped_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('set null');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('contact_name');
            $table->string('contact_email')->nullable();
            $table->integer('score')->default(0);
            $table->string('intent_category')->nullable();
            $table->string('status')->default('New'); // New, Discovered, Contacted, Qualified, Closed
            $table->text('notes')->nullable();
            $table->text('generated_reply')->nullable(); // Pre-drafted AI reply
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->json('messages')->nullable(); // Chat dialogue log
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('agent_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('memory_key');
            $table->text('memory_value');
            $table->string('type')->default('semantic'); // system, knowledge, semantic
            $table->timestamps();
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->string('meeting_link')->nullable();
            $table->string('status')->default('Scheduled'); // Scheduled, Completed, Cancelled
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tier')->default('Free'); // Free, Pro, Enterprise
            $table->string('status')->default('Active'); // Active, Expired
            $table->integer('credits')->default(0);
            $table->json('limits_json')->nullable();
            $table->timestamp('billing_cycle_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('status')->default('Unpaid'); // Paid, Unpaid
            $table->date('invoice_date');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('target_table')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('credit_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('action');
            $table->integer('credits_used');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_usage_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('agent_memories');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('platform_accounts');
        Schema::dropIfExists('projects');
    }
};
