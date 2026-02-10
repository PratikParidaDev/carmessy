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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id');
            $table->enum('vehicle_type', ['car', 'bike'])->default('car');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number', 20);
            $table->string('city');
            $table->date('preferred_booking_date');
            $table->string('preferred_time_slot');
            $table->string('pickup_type'); // Changed from enum to string for dynamic values
            $table->string('payment_mode'); // Changed from enum to string for dynamic values
            $table->string('id_proof_type'); // Changed from enum to string for dynamic values
            $table->string('id_proof_number');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('user_id');
            $table->index(['vehicle_id', 'vehicle_type']);
            $table->index('status');
            $table->index('preferred_booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
