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
        Schema::table('tbl_inside_ec', function (Blueprint $table) {
            $table->string('municipality_located_ec_psgc_code')->after('municipality_psgc_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_inside_ec', function (Blueprint $table) {
            $table->dropColumn('municipality_located_ec_psgc_code');
        });
    }
};
