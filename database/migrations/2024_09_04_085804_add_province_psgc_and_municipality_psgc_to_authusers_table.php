<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('auth_users', function (Blueprint $table) {
            $table->string('province_psgc'); // Add non-nullable province_psgc column
            $table->string('municipality_psgc'); // Add non-nullable municipality_psgc column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auth_users', function (Blueprint $table) {
            $table->dropColumn('province_psgc'); // Drop province_psgc column
            $table->dropColumn('municipality_psgc'); // Drop municipality_psgc column
        });
    }
};
