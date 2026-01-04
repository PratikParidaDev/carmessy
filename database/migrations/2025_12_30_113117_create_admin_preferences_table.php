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
        Schema::create('admin_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('sidebar_bg', 7)->default('#23282d');
            $table->string('sidebar_hover', 7)->default('#32373c');
            $table->string('sidebar_text', 7)->default('#b4b9be');
            $table->string('sidebar_active', 7)->default('#0073aa');
            $table->string('content_bg', 7)->default('#f0f0f1');
            $table->string('primary_color', 7)->default('#2271b1');
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_preferences');
    }
};
