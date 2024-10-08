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
        Schema::create('tbl_evacuation_centers', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('province_psgc_code');
            $table->string('municipality_psgc_code');
            $table->string('brgy_psgc_code');
            $table->string('evacuation_center_name');
            $table->text('description')->nullable();
            $table->string('evacuation_center_type')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('camp_manager_name')->nullable();
            $table->string('camp_manager_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evacuation_centers');
    }
};
