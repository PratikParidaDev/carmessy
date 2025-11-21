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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('featured_listings_used')->default(0);
            $table->integer('regular_listings_used')->default(0);
            $table->timestamps();
            
            $table->index('dealer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
