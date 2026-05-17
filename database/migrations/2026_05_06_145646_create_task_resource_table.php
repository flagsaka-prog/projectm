<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks')->onDelete('cascade');
            $table->foreignId('resource_id')
                ->constrained('resources')->onDelete('cascade');
            $table->integer('quantity_used')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['task_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_resource');
    }
};
