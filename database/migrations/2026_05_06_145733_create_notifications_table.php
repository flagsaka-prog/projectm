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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->comment('Misal: task_assigned, deadline_approaching, status_changed');
            $table->json('data')->nullable()->comment('Detail notifikasi dalam format JSON');
            $table->timestamp('read_at')->nullable()->comment('Null = belum dibaca');
            $table->timestamps();

            // Index
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
