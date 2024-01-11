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

		Schema::create('survey_question_list_types', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->uuid('client_id')->notNullable();
			$table->string('name',50)->unique()->notNullable();
			$table->string('description',255)->nullable();
			$table->timestamps();
		});
	
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('survey_question_list_types');
	}

};
