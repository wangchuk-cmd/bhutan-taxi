<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Optional: Add cached rating columns to drivers table for performance optimization
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 2)->default(0)->after('active');
            $table->integer('rating_count')->default(0)->after('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['average_rating', 'rating_count']);
        });
    }
};
