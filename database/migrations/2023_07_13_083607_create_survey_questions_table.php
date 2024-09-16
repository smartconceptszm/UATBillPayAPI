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

		Schema::create('survey_questions', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('survey_id',36)->notNullable();
			$table->string('order')->notNullable();
			$table->string('prompt',150)->notNullable();
			$table->enum('type',['MOBILE','LIST','DATE','NATIONALID','ONEWORD','GENERAL'])->default('GENERAL')->notNullable();
			$table->string('survey_question_list_type_id')->nullable();
			$table->unique(['survey_id', 'order'],'survey_question_order');
			$table->timestamps();
		});
	
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('survey_questions');
	}

};
