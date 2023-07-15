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
      Schema::create('service_applications', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('service_type_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->nullable();
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
      Schema::dropIfExists('service_applications');
   }
};
