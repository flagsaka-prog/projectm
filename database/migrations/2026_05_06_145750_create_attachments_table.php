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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('filename')->comment('Nama file asli');
            $table->string('filepath')->comment('Path penyimpanan di storage');
            $table->bigInteger('size')->nullable()->comment('Ukuran file dalam bytes');
            $table->string('mime_type', 100)->nullable()->comment('Tipe file: image/png, application/pdf, dll');
            $table->softDeletes();
            $table->timestamps();

            // Index
            $table->index('task_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
