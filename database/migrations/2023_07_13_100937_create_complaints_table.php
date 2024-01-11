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
         $table->uuid('id')->primary();
         $table->uuid('complaint_subtype_id')->notNullable();
         $table->uuid('client_id')->notNullable();
         $table->uuid('session_id')->nullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('address',255)->nullable();
         $table->string('details')->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
         $table->uuid('assignedBy')->nullable();
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
