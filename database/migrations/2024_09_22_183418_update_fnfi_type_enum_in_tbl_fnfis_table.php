<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFnfiTypeEnumInTblFnfisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
    //     // Add the new 'Financial' enum value to the existing type
    //     DB::statement("ALTER TYPE public.fnfi_type ADD VALUE 'Financial'");

    //     // If fnfi_type_enum is not your actual type, replace it with the correct enum type name
    // }
    // public function up()
    // {
    //     // Update the column to add a new enum value 'Financial'
    //     DB::statement("ALTER TABLE tbl_fnfis ALTER COLUMN fnfi_type ENUM('Food Item', 'Non-Food Item, 'Financial') NOT NULL");
    // }
    public function up()
    {
        DB::statement("ALTER TABLE tbl_fnfis DROP CONSTRAINT tbl_fnfis_fnfi_type_check");

        $types = ['Food Item', 'Non-Food Item', 'Financial'];
        $result = join( ', ', array_map(function ($value){
            return sprintf("'%s'::character varying", $value);
        }, $types));

        DB::statement("ALTER TABLE tbl_fnfis ADD CONSTRAINT tbl_fnfis_fnfi_type_check CHECK (fnfi_type::text = ANY (ARRAY[$result]::text[]))");
    }
}

