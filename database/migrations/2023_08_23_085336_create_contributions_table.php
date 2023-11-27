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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
			$table
				->unsignedBigInteger('participant_id')
				->nullable()
				->foreign('participant_id')
				->references('id')
				->on('participants');

			$table->text('year')->nullable();
			$table->text('month')->nullable();
			$table->boolean('attendance_list_received')->nullable();
			$table->double('company_basic_contribution')->nullable();
			$table->double('basic_scholarship')->nullable();
			$table->double('additional_scholarship')->nullable();
			$table->double('foundation_management')->nullable();
			$table->double('course_cost')->nullable();

			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
