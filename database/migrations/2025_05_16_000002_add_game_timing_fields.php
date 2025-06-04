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
            $table->timestamp('started_at')->nullable()->after('duration');
            $table->timestamp('stopped_at')->nullable()->after('started_at');
            $table->integer('accumulated_duration')->default(0)->after('stopped_at')
                ->comment('Total accumulated duration in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'stopped_at', 'accumulated_duration']);
        });
    }
};
