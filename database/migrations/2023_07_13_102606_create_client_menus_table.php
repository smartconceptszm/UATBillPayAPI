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
      Schema::create('client_menus', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->uuid("client_id")->notNullable();
         $table->uuid("parent_id")->notNullable()->default(0);
         $table->integer("order")->notNullable();
         $table->string('prompt',50)->notNullable();
         $table->string('handler',50)->notNullable();
         $table->string('billingClient',50)->nullable();
         $table->string('description',150)->nullable();
         $table->enum('isPayment',['YES','NO'])->default('NO')->notNullable();
         $table->string('receiptingHandler',50)->nullable();
         $table->enum('isDefault',['YES','NO'])->default('NO')->notNullable();
         $table->enum('isActive',['YES','NO'])->default('NO')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_mnos');
   }
};
