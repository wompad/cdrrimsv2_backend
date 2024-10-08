<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function up()
   {
       Schema::create('tbl_fnfi_assistance', function (Blueprint $table) {
           $table->uuid('uuid')->primary();
           $table->uuid('disaster_report_uuid');
           $table->uuid('fnfi_uuid');
           $table->decimal('fnfi_cost', 10, 2);
           $table->integer('fnfi_quantity');
           $table->date('augmentation_date');
           $table->string('province_psgc_code');
           $table->string('municipality_psgc_code');
           $table->timestamps(); // Automatically includes 'created_at' and 'updated_at'
       });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
       Schema::dropIfExists('tbl_fnfi_assistance');
   }
};
