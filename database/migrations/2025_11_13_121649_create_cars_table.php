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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('make_id')->constrained();
            $table->foreignId('model_id')->constrained();
            $table->foreignId('dealer_id')->constrained();
            $table->foreignId('city_id')->constrained();
            
            // Basic Details
            $table->integer('year');
            $table->decimal('price', 12, 2);
            $table->enum('condition', ['new', 'used', 'certified'])->default('used');
            $table->integer('mileage')->nullable(); // in km
            $table->string('vin')->nullable()->unique();
            $table->string('registration_number')->nullable();
            
            // Specifications
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'cng', 'lpg']);
            $table->enum('transmission', ['manual', 'automatic', 'semi-automatic']);
            $table->string('engine_capacity')->nullable(); // e.g., "1.5L", "2000cc"
            $table->integer('power')->nullable(); // in HP
            $table->integer('torque')->nullable(); // in Nm
            $table->decimal('mileage_kmpl', 5, 2)->nullable(); // fuel efficiency
            $table->string('exterior_color');
            $table->string('interior_color')->nullable();
            $table->integer('seats')->default(5);
            $table->integer('doors')->default(4);
            
            // Features (JSON)
            $table->json('features')->nullable();
            $table->json('safety_features')->nullable();
            
            // Ownership & History
            $table->integer('owners')->default(1);
            $table->boolean('insurance_valid')->default(false);
            $table->date('insurance_expiry')->nullable();
            $table->boolean('under_warranty')->default(false);
            $table->text('service_history')->nullable();
            
            // Description
            $table->text('description')->nullable();
            
            // Status & Verification
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->text('admin_notes')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Stats
            $table->integer('views')->default(0);
            $table->integer('inquiries')->default(0);
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for search performance
            $table->index('slug');
            $table->index(['make_id', 'model_id']);
            $table->index('dealer_id');
            $table->index('city_id');
            $table->index('status');
            $table->index('condition');
            $table->index(['price', 'year']);
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
