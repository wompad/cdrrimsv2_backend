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
        Schema::create('tbl_sex_age_sector_data', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('province_psgc_code');
            $table->string('municipality_psgc_code');
            $table->uuid('disaster_report_uuid');

            // Demographic fields
            $table->integer('infant_male_cum')->nullable();
            $table->integer('infant_male_now')->nullable();
            $table->integer('infant_female_cum')->nullable();
            $table->integer('infant_female_now')->nullable();

            $table->integer('toddlers_male_cum')->nullable();
            $table->integer('toddlers_male_now')->nullable();
            $table->integer('toddlers_female_cum')->nullable();
            $table->integer('toddlers_female_now')->nullable();

            $table->integer('school_age_male_cum')->nullable();
            $table->integer('school_age_male_now')->nullable();
            $table->integer('school_age_female_cum')->nullable();
            $table->integer('school_age_female_now')->nullable();

            $table->integer('teenage_male_cum')->nullable();
            $table->integer('teenage_male_now')->nullable();
            $table->integer('teenage_female_cum')->nullable();
            $table->integer('teenage_female_now')->nullable();

            $table->integer('adult_male_cum')->nullable();
            $table->integer('adult_male_now')->nullable();
            $table->integer('adult_female_cum')->nullable();
            $table->integer('adult_female_now')->nullable();

            $table->integer('elderly_male_cum')->nullable();
            $table->integer('elderly_male_now')->nullable();
            $table->integer('elderly_female_cum')->nullable();
            $table->integer('elderly_female_now')->nullable();

            $table->integer('pregnant_cum')->nullable();
            $table->integer('pregnant_now')->nullable();

            $table->integer('lactating_cum')->nullable();
            $table->integer('lactating_now')->nullable();

            $table->integer('child_headed_male_cum')->nullable();
            $table->integer('child_headed_male_now')->nullable();
            $table->integer('child_headed_female_cum')->nullable();
            $table->integer('child_headed_female_now')->nullable();

            $table->integer('single_headed_male_cum')->nullable();
            $table->integer('single_headed_male_now')->nullable();
            $table->integer('single_headed_female_cum')->nullable();
            $table->integer('single_headed_female_now')->nullable();

            $table->integer('solo_parent_male_cum')->nullable();
            $table->integer('solo_parent_male_now')->nullable();
            $table->integer('solo_parent_female_cum')->nullable();
            $table->integer('solo_parent_female_now')->nullable();

            $table->integer('pwd_male_cum')->nullable();
            $table->integer('pwd_male_now')->nullable();
            $table->integer('pwd_female_cum')->nullable();
            $table->integer('pwd_female_now')->nullable();

            $table->integer('ip_male_cum')->nullable();
            $table->integer('ip_male_now')->nullable();
            $table->integer('ip_female_cum')->nullable();
            $table->integer('ip_female_now')->nullable();

            $table->integer('fourps_male_cum')->nullable();
            $table->integer('fourps_male_now')->nullable();
            $table->integer('fourps_female_cum')->nullable();
            $table->integer('fourps_female_now')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sex_age_sector_data');
    }
};
