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
        Schema::create('pvas', function (Blueprint $table) {
            $table->id();
            $table->integer('salutation')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone_number')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->string('region')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pva');
    }
};
