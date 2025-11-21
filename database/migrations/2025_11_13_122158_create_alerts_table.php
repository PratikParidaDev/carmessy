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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Alert name
            $table->foreignId('make_id')->nullable()->constrained();
            $table->foreignId('model_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();
            $table->integer('min_year')->nullable();
            $table->integer('max_year')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'cng', 'lpg'])->nullable();
            $table->enum('transmission', ['manual', 'automatic', 'semi-automatic'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
