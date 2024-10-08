<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblOutsideEcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_outside_ec', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('disaster_report_uuid');
            $table->string('host_province_psgc_code');
            $table->string('host_municipality_psgc_code');
            $table->string('host_brgy_psgc_code');
            $table->integer('aff_families_cum');
            $table->integer('aff_families_now');
            $table->integer('aff_persons_cum');
            $table->integer('aff_persons_now');
            $table->string('origin_province_psgc_code');
            $table->string('origin_municipality_psgc_code');
            $table->string('origin_brgy_psgc_code');
            $table->timestamps(); // Optional if you want created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_outside_ec');
    }
}

