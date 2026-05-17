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
        Schema::table('resources', function (Blueprint $table) {
            $table->string('initials')->nullable()->after('name');
            $table->string('group')->nullable()->after('initials');
            $table->integer('max_units')->default(0)->after('group');
            $table->decimal('std_rate', 15, 2)->default(0)->after('max_units');
            $table->decimal('ovt_rate', 15, 2)->default(0)->after('std_rate');
            $table->decimal('cost_per_use', 15, 2)->default(0)->after('ovt_rate');
            $table->string('accrue_at')->default('start')->after('cost_per_use');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['initials', 'group', 'max_units', 'std_rate', 'ovt_rate', 'cost_per_use', 'accrue_at']);
        });
    }
};
