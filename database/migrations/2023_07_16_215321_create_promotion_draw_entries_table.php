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
      Schema::create('promotion_draw_entries', function (Blueprint $table) {
         $table->id();
         $table->unsignedInteger('promotion_id');
         $table->string("customerAccount",50)->notNullable();
         $table->string("mobileNumber",15)->nullable();
         $table->timestamp('entryDate');
         $table->float('paymentAmount',10,2)->default(0);
         $table->string("receiptNumber",50)->nullable();
         $table->timestamp('raffleDate');
         $table->enum('raffleWinner',['NO','YES'])->default('NO')->notNullable();
         $table->integer('drawNumber')->nullable();
         $table->string("drawMessage",150)->nullable();
         $table->enum('status',['RECORDED','REDEEEMED'])->default('RECORDED')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('promotion_draw_entries');
   }
   
};
