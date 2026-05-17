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
        Schema::create('working_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->enum('type', ['holiday', 'day_off', 'working'])->comment('holiday: libur nasional, day_off: libur perusahaan, working: hari kerja pengganti');
            $table->string('description')->nullable()->comment('Misal: Hari Raya Idul Fitri');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Index
            $table->index('date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_calendars');
    }
};
