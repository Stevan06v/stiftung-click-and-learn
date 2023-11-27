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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('companyname1');
            $table->string('companyname2')->nullable();
            $table->integer('salutation')->nullable();
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->string('phone_number')->nullable();
            $table->string('fax')->nullable();
            $table->string('phone_number_mobil')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->boolean('cooperation_agreement')->default(false);
            $table->text('note')->nullable();
            $table->text('hour_record')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companys');
    }
};
