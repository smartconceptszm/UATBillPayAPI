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
      Schema::create('promotion_entries', function (Blueprint $table) {
         $table->id();
         $table->unsignedInteger('promotion_id');
         $table->string("payment_id",36)->notNullable();
         $table->timestamp('entryDate');
         $table->string("customerAccount",50)->notNullable();
         $table->string("mobileNumber",15)->nullable();
         $table->float('paymentAmount',10,2)->default(0);
         $table->string("receiptNumber",50)->nullable();
         $table->float('rewardAmount',10,2)->default(0);
         $table->float('rewardRate',10,2)->default(0);
         $table->string("message",150)->nullable();
         $table->enum('smsDelivered',['YES','NO'])->default('NO')->notNullable();
         $table->enum('status',['RECORDED','REDEEEMED'])->default('RECORDED')->notNullable();
         $table->date('dateRedeemed');
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('promotion_entries');
   }
   
};
