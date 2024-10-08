<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDromicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dromic', function (Blueprint $table) {
            $table->uuid('uuid')->primary();  // UUID as the primary key
            $table->string('incident_name');
            $table->date('incident_date');
            $table->unsignedBigInteger('created_by');  // Foreign key or reference to the user who created it
            $table->timestamps();  // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_dromic');
    }
}
