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
        Schema::create('tbl_assistance_cost', function (Blueprint $table) {

            $table->uuid('uuid')->primary();
            $table->uuid('disaster_report_uuid');
            $table->string('province_psgc_code');
            $table->string('municipality_psgc_code');
            $table->decimal('lgu_assistance', 15, 2)->nullable();
            $table->decimal('ngo_assistance', 15, 2)->nullable();
            $table->decimal('other_go_assistance', 15, 2)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_assistance_cost');
    }
};
