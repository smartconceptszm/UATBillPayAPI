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
         $table->string("client_id",36)->notNullable();
         $table->string("parent_id",36)->notNullable()->default(0);
         $table->integer("order")->notNullable();
         $table->string('prompt',50)->notNullable();
         $table->string('handler',50)->notNullable();
         $table->string('billingClient',50)->nullable();
         $table->string('description',150)->nullable();
         $table->enum('isPayment',['YES','NO'])->default('NO')->notNullable();
         $table->string('receiptingHandler',50)->nullable();
         $table->enum('isDefault',['YES','NO'])->default('NO')->notNullable();
         $table->enum('isActive',['YES','NO'])->default('NO')->notNullable();
         $table->string('cAccountCode',50)->nullable();
         $table->enum('onOneAccount',['YES','NO'])->default('NO')->notNullable();
         $table->string('commonAccount',50)->nullable();
         $table->string('customerAccountPrompt',150)->nullable();
         $table->enum('requiresReference',['YES','NO'])->default('NO')->notNullable();
         $table->string('referencePrompt',150)->nullable();
         $table->string('shortcutHandler',150)->nullable();
         $table->string('shortcut',150)->nullable();
         $table->timestamps();
         $table->unique(['client_id','parent_id','order'],'menu_order');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_menus');
   }
};
