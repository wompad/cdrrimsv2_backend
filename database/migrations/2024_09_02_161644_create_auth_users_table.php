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
        Schema::create('auth_users', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('firstname'); // First name field
            $table->string('lastname'); // Last name field
            $table->string('email_address')->unique(); // Email address, unique
            $table->string('password'); // Password field
            $table->enum('user_type', ['Admin', 'User']); // User type (Admin or User)
            $table->boolean('is_activated')->default(false); // Activation status
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_users');
    }
};
