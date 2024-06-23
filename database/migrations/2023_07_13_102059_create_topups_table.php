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
      Schema::create('topups', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("client_id",36)->notNullable();
         $table->float('amount',10,2)->default(0);
         $table->enum('approval_status',['PENDING','APPROVED','REJECTED'])->default('PENDING')->notNullable();
         $table->unsignedBigInteger('initiatedBy')->notNullable();
         $table->unsignedBigInteger('approvedBy')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('topups');
   }
};
