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
      Schema::create('dashboard_snippets', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("client_id",36)->notNullable();
         $table->tinyInteger("xPosition")->notNullable();
         $table->tinyInteger("yPosition")->notNullable();
         $table->enum('sizeOnPage',['FULL','HALF'])->default('HALF')->notNullable();
         $table->string('title',150)->notNullable();
         $table->string('type',50)->notNullable();
         $table->string('generateHandler',50)->notNullable();
         $table->string('viewHandler',50)->notNullable();
         $table->enum('hasDrillDown',['YES','NO'])->default('NO')->notNullable();
         $table->string('label',150)->nullable();
         $table->string('backgroundColour',50)->nullable();
         $table->string('borderColour',150)->nullable();
         $table->enum('isActive',['YES','NO'])->default('YES')->notNullable();
         $table->timestamps();
         $table->unique(['client_id','xPosition'],'dashboard_order');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_snippets');
   }
};
