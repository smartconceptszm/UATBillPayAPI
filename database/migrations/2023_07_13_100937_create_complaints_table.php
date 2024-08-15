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
         $table->string('complaint_subtype_id',36)->notNullable();
         $table->string('client_id',36)->notNullable();
         $table->string('session_id',36)->nullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('customerAccount',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('address',255)->nullable();
         $table->string('details')->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
         $table->string('assignedBy',36)->nullable();
         $table->string('assignedTo',36)->nullable();
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
