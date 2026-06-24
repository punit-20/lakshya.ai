<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agent_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('agent_type'); // LeadHunterAgent, ContentAgent, SEOAgent, EmailAgent, WhatsAppAgent, LinkedInAgent, etc.
            $table->string('task_name');
            $table->json('payload')->nullable();
            $table->string('status')->default('Pending'); // Pending, Running, Completed, Failed
            $table->timestamps();
        });

        Schema::create('agent_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_task_id')->constrained('agent_tasks')->onDelete('cascade');
            $table->string('status')->default('Processing'); // Processing, Success, Failed
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->json('result_data')->nullable();
            $table->timestamps();
        });

        Schema::create('agent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_run_id')->constrained('agent_runs')->onDelete('cascade');
            $table->string('level')->default('INFO'); // INFO, WARNING, ERROR
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('visitor_hits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('ip_address');
            $table->string('company_name')->nullable();
            $table->json('pages_visited'); // array of visited paths
            $table->integer('intent_score')->default(0);
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('phone_number');
            $table->text('message');
            $table->string('status')->default('Sent'); // Sent, Delivered, Replied
            $table->text('reply_message')->nullable();
            $table->string('sentiment')->nullable(); // Positive, Negative, Neutral
            $table->timestamps();
        });

        Schema::create('linkedin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('profile_url');
            $table->string('action_type'); // Profile Visit, Connection Request, Custom Message, Follow-up
            $table->text('message')->nullable();
            $table->string('status')->default('Completed'); // Pending, Completed, Failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linkedin_logs');
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('visitor_hits');
        Schema::dropIfExists('agent_logs');
        Schema::dropIfExists('agent_runs');
        Schema::dropIfExists('agent_tasks');
    }
};
