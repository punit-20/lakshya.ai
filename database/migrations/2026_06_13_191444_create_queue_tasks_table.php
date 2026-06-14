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
        Schema::create('queue_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('task_type');
            $table->text('payload'); // holds JSON config / prompts
            $table->string('status')->default('Pending'); // Pending, Processing, Completed, Failed
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->text('result_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tasks');
    }
};
