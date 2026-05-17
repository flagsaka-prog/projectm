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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['error', 'warning', 'info', 'debug'])->default('info');
            $table->text('message');
            $table->json('context')->nullable()->comment('Stack trace, request data, dll');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Null jika dipicu sistem');
            $table->string('url', 500)->nullable()->comment('URL request yang memicu');
            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index('level');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
