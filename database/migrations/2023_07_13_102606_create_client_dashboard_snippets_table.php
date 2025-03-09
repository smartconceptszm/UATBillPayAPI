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

      Schema::create('client_dashboard_snippets', function (Blueprint $table) {
         $table->id();
         $table->smallInteger("dashboard_id")->notNullable();
         $table->integer("dashboard_snippet_id")->notNullable();
         $table->tinyInteger("rowNumber")->notNullable();
         $table->tinyInteger("columnNumber")->notNullable();
         $table->enum('sizeOnPage',['FULL','HALF'])->default('HALF')->notNullable();
         $table->string('viewHandler',150)->notNullable();
         $table->string('hyperlink',150)->nullable();
         $table->enum('isActive',['YES','NO'])->default('YES')->notNullable();
         $table->timestamps();
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
