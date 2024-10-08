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
        Schema::create('lib_barangays', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code');
            $table->string('name');
            $table->string('correspondence_code');
            $table->string('geographic_level');
            $table->integer('population_2020');
            $table->string('municipality_psgc_code');
            $table->string('province_psgc_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_barangays');
    }
};
