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
        Schema::create('ams_rgs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->string('email');
            $table->string('phone_number');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ams_rgs');
    }
};
