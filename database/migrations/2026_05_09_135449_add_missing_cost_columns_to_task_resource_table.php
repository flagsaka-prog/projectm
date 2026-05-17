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
            $table->decimal('quantity', 10, 2)->default(0)->after('allocation_percent');
            $table->decimal('estimated_cost', 15, 2)->default(0)->after('quantity');
            $table->decimal('actual_cost', 15, 2)->default(0)->after('estimated_cost');
        });
    }

    public function down(): void
    {
        Schema::table('task_resource', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'estimated_cost', 'actual_cost']);
        });
    }
};
