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
         $table->id();
         $table->unsignedBigInteger('survey_entry_id')->notNullable();
         $table->unsignedBigInteger('survey_question_id')->notNullable();
         $table->string('answer')->notNullable();
         $table->timestamps();
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
