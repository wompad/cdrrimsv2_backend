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
        Schema::table('tbl_sex_age_sector_data', function (Blueprint $table) {
            $table->integer('preschoolers_male_cum')->nullable();
            $table->integer('preschoolers_male_now')->nullable();
            $table->integer('preschoolers_female_cum')->nullable();
            $table->integer('preschoolers_female_now')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_sex_age_sector_data', function (Blueprint $table) {
            $table->dropColumn('preschoolers_male_cum');
            $table->dropColumn('preschoolers_male_now');
            $table->dropColumn('preschoolers_female_cum');
            $table->dropColumn('preschoolers_female_now');
        });
    }
};
