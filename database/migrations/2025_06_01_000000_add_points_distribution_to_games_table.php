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
        Schema::table('games', function (Blueprint $table) {
            $table->integer('total_points')->default(9)->comment('Total points to distribute in this game');
            $table->integer('points_recipients')->default(3)->comment('Number of players who receive points');
            $table->json('points_distribution')->nullable()->comment('Custom points distribution per placement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('total_points');
            $table->dropColumn('points_recipients');
            $table->dropColumn('points_distribution');
        });
    }
};
