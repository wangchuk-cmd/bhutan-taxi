<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedTinyInteger('years_of_experience')->nullable()->after('fuel_type');
            $table->boolean('show_experience_to_public')->default(false)->after('years_of_experience');
            $table->boolean('show_age_range_to_public')->default(false)->after('show_experience_to_public');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['years_of_experience', 'show_experience_to_public', 'show_age_range_to_public']);
        });
    }
};
