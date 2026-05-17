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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 100)->comment('Misal: create_task, update_status, assign_project');
            $table->string('module', 100)->comment('Misal: Project, Task, Resource, User');
            $table->string('subject_type', 100)->nullable()->comment('Misal: App\Models\Task');
            $table->bigInteger('subject_id')->nullable()->comment('ID record yang diubah');
            $table->json('old_values')->nullable()->comment('Nilai sebelum perubahan');
            $table->json('new_values')->nullable()->comment('Nilai setelah perubahan');
            $table->timestamps();

            // Index
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
