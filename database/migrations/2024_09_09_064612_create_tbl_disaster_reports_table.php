<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDisasterReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_disaster_reports', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('incident_id');
            $table->string('report_name');
            $table->date('report_date');
            $table->time('as_of_time');
            $table->string('prepared_by');
            $table->string('recommended_by');
            $table->string('approved_by');
            $table->string('prepared_by_position');
            $table->string('recommended_by_position');
            $table->string('approved_by_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_disaster_reports');
    }
}
