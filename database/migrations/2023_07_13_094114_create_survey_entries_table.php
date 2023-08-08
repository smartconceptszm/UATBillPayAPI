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
      Schema::create('survey_entries', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('survey_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',12)->nullable();
         $table->enum('status',['INITIATED','SUBMITTED','ASSIGNED','CLOSED'])->default('INITIATED')->notNullable();
         $table->unsignedBigInteger('assignedBy')->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('survey_entries');
   }
};