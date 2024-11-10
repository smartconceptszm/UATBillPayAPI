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
      Schema::create('customer_field_updates', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('client_id',36)->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('customerAccount',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->enum('status',['INITIATED','SUBMITTED','ASSIGNED','CLOSED'])->default('INITIATED')->notNullable();
         $table->string('assignedBy',36)->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
         $table->unique('caseNumber','customer_field_updates_casenumber_unique');
         $table->index('client_id');
         $table->index('created_at');
         $table->index('status');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('customer_field_updates');
   }
};
