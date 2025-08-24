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
      Schema::create('promotions', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->string("name",150)->notNullable();
         $table->string("description",150)->nullable();
         $table->enum('consumerType',['DOMESTIC','COMMERCIAL','INSTITUTIONAL','ALL'])->default('DOMESTIC')->notNullable();
         $table->float('entryAmount',10,2)->default(0);
         $table->float('raffleEntryAmount',10,2)->default(0);
         $table->integer('raffleDrawTimeout')->default(10);
         $table->enum('onDebt',['NO','YES'])->default('YES')->notNullable();
         $table->enum('type',['TIERED','FLATRATE'])->default('FLATRATE')->notNullable();
         $table->enum('resetMonthly',['NO','YES'])->default('YES')->notNullable();
         $table->float('rateValue',10,2)->default(0);
         $table->timestamp('startDate');
         $table->timestamp('endDate');
         $table->string("entryMessage",160)->nullable();
         $table->string("raffleEntryMessage",150)->nullable();
         $table->enum('status',['ACTIVE', 'CLOSED'])->default('ACTIVE')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('promotions');
   }
   
};
