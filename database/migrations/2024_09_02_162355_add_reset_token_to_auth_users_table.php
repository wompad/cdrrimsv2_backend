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
        Schema::table('auth_users', function (Blueprint $table) {
            $table->string('reset_token')->nullable(); // Add reset token column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auth_users', function (Blueprint $table) {
            $table->dropColumn('reset_token'); // Drop the reset token column
        });
    }
};
