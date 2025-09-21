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
      Schema::create('raffle_draw_entries', function (Blueprint $table) {
         $table->id();
         $table->unsignedInteger('promotion_id');
         $table->unsignedInteger('promotion_entry_id')->nullable();
         $table->string("customerAccount",50)->notNullable();
         $table->string("consumerType",50)->notNullable();
         $table->string("mobileNumber",15)->nullable();
         $table->timestamp('entryDate');
         $table->float('paymentAmount',10,2)->default(0);
         $table->string("receiptNumber",50)->nullable();
         $table->timestamp('raffleDate');
         $table->integer('drawNumber')->nullable();
         $table->string("winMessage",150)->nullable();
         $table->enum('status',['RECORDED','WINNER','REDEEEMED'])->default('RECORDED')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('raffle_draw_entries');
   }
   
};
