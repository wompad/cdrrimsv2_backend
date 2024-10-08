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
        Schema::create('tbl_inside_ec', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('province_psgc_code');
            $table->string('municipality_psgc_code');
            $table->string('brgy_located_ec_psgc_code');
            $table->uuid('ec_uuid'); // Assuming this is a UUID for evacuation center
            $table->integer('ec_cum')->default(0);
            $table->integer('ec_now')->default(0);
            $table->integer('families_cum')->default(0);
            $table->integer('families_now')->default(0);
            $table->integer('persons_cum')->default(0);
            $table->integer('persons_now')->default(0);
            $table->text('brgy_origin_psgc_codes'); // Assuming this could store multiple values
            $table->string('ec_status');
            $table->text('ec_remarks')->nullable();
            $table->uuid('disaster_report_uuid'); // Assuming this is a UUID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_inside_ec');
    }
};
