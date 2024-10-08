<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_damage_per_brgy', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('province_psgc_code');
            $table->string('municipality_psgc_code');
            $table->string('brgy_psgc_code');
            $table->integer('totally_damaged')->nullable()->default(0);
            $table->integer('partially_damaged')->nullable()->default(0);
            $table->uuid('disaster_report_uuid');
            $table->decimal('cost_asst_brgy', 15, 2)->nullable()->default(0.00);
            $table->integer('affected_families')->default(0);
            $table->integer('affected_persons')->default(0);
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
        Schema::dropIfExists('tbl_damage_per_brgy');
    }
};
