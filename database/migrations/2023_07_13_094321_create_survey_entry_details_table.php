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
      Schema::create('survey_entry_details', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('survey_entry_id',36)->notNullable();
         $table->string('survey_question_id',36)->notNullable();
         $table->string('answer')->notNullable();
         $table->timestamps();
         $table->unique(['survey_entry_id','survey_question_id'],'survey_entry_id');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('survey_entry_details');
   }
};
