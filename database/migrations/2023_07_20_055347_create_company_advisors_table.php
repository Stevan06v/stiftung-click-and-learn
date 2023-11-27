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
        Schema::create('company_advisors', function (Blueprint $table) {
            $table->id();
            $table->integer('salutation')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table
				->unsignedBigInteger('company_id')
				->nullable()
				->foreign('company_id')
				->references('id')
				->on('companies');
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
        Schema::dropIfExists('company_advisors');
    }
};
