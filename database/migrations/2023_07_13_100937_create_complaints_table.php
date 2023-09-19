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
      Schema::create('complaints', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('complaint_subtype_id')->notNullable();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('session_id')->nullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('address',255)->nullable();
         $table->string('details')->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
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
      Schema::dropIfExists('complaints');
   }
};
