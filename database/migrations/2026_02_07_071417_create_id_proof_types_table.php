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
        Schema::create('id_proof_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Aadhar", "Driving License", "Passport"
            $table->string('slug')->unique();
            $table->string('display_name')->nullable(); // For custom display
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_proof_types');
    }
};
