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
        Schema::create('ams_advisors', function (Blueprint $table) {
            $table->id();
            $table->integer('salutation')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->unsignedBigInteger('ams_rgs_id')->nullable()->foreign('ams_rgs_id')->references('id')->on('ams_rgs');
            $table->integer('function');
            $table->boolean('department_head')->default(false);
            $table->string('phone_number')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ams_advisor');
    }
};
