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
        Schema::create('tbl_fnfis', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('fnfi_name');
            $table->string('description');
            $table->enum('fnfi_type', ['Food Item', 'Non-Food Item']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fnfis');
    }
};
