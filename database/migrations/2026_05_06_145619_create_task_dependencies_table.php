<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks')->onDelete('cascade');
            $table->foreignId('depends_on_task_id')
                ->constrained('tasks')->onDelete('cascade');
            $table->enum('type', ['FS', 'SS', 'FF', 'SF'])->default('FS');
            // FS = Finish to Start (paling umum)
            // SS = Start to Start
            // FF = Finish to Finish
            // SF = Start to Finish
            $table->integer('lag_days')->default(0); // jeda hari antar task
            $table->timestamps();

            // Satu pasang task tidak boleh duplikat
            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
