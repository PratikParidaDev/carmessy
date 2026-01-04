<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the ENUM column to include super_admin
        // The original enum was: 'buyer', 'dealer', 'admin'
        // We need to add 'user' and 'super_admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'buyer', 'dealer', 'admin', 'super_admin') DEFAULT 'buyer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (remove super_admin and user)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('buyer', 'dealer', 'admin') DEFAULT 'buyer'");
    }
};
