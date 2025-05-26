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
         $table->timestamp('entryDate');
         $table->string("customerAccount",50)->notNullable();
         $table->float('receiptAmount',10,2)->default(0);
         $table->float('rewardAmount',10,2)->default(0);
         $table->float('rewardRate',10,2)->default(0);
         $table->string("message",150)->nullable();
         $table->enum('drawnInRaffle',['NO','YES'])->default('NO')->notNullable();
         $table->integer('drawNumber')->nullable();
         $table->string("drawMessage",150)->nullable();
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
