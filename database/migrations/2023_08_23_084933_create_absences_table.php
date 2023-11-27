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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
			$table
				->unsignedBigInteger('participant_id')
				->nullable()
				->foreign('participant_id')
				->references('id')
				->on('participants');
			$table->text('type')->nullable();
			$table
				->date('start_date')
				->nullable();
			$table
				->date('end_date')
				->nullable();
			$table
				->double('business_days')
				->nullable();
			$table
				->text('annotation')
				->nullable();

			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
