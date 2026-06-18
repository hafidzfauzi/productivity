<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('gamification_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('total_xp')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_active_date')->nullable();
            $table->timestamps();
        });

        // Insert default row
        DB::table('gamification_stats')->insert([
            'total_xp' => 0,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_active_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_stats');
    }
};
