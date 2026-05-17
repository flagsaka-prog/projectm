<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE resources MODIFY COLUMN type ENUM('equipment','tool','material','other','human') NOT NULL DEFAULT 'other'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resources MODIFY COLUMN type ENUM('equipment','tool','material','other') NOT NULL DEFAULT 'other'");
    }
};
