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
		
		Schema::create('survey_question_list_items', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->uuid('survey_question_list_type_id')->notNullable();
			$table->string('value',30)->notNullable();
			$table->unsignedTinyInteger('order')->notNullable();
			$table->unique(['survey_question_list_type_id', 'order'],'list_type_order');
			$table->timestamps();
		});
	
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('survey_question_list_items');
	}

};
