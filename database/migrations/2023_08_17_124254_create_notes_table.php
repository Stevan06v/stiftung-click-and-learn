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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
			$table->text('title')->nullable();
			$table->text('text')->nullable();
			$table
				->unsignedBigInteger('participant_id')
				->nullable()
				->foreign('participant_id')
				->references('id')
				->on('participants');
			$table
				->date('note_date')
				->nullable();
			$table
				->unsignedBigInteger('user_id')
				->nullable()
				->foreign('user_id')
				->references('id')
				->on('users');
			$table->text('note_files')->nullable();
			$table->text('original_filenames')->nullable();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
