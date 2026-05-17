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
        Schema::table('task_resource', function (Blueprint $table) {
            $table->integer('allocation_percent')->default(100)->after('resource_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_resource', function (Blueprint $table) {
            $table->dropColumn('allocation_percent');
        });
    }
};
