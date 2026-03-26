<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedInteger('total_points')->default(9)->after('duration');
            $table->json('points_distribution')->nullable()->after('total_points');
        });

        DB::table('games')->update([
            'total_points' => 9,
            'points_distribution' => json_encode([5, 3, 1], JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['total_points', 'points_distribution']);
        });
    }
};
