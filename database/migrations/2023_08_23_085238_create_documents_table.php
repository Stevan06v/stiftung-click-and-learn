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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
			$table
				->unsignedBigInteger('participant_id')
				->nullable()
				->foreign('participant_id')
				->references('id')
				->on('participants');
			$table
				->text('designation')
				->nullable();
			$table
				->text('training_provider')
				->nullable();
			$table
				->date('start_date')
				->nullable();
			$table
				->date('end_date')
				->nullable();
			$table
				->date('date')
				->nullable();
			$table
				->text('invoice_number')
				->nullable();
			$table
				->double('amount')
				->nullable();
			$table
				->boolean('certificate')
				->nullable();
			$table
				->double('referral')
				->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
