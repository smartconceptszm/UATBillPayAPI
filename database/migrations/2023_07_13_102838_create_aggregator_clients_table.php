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
      Schema::create('aggregated_clients', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('parent_id',36)->notNullable();
         $table->string('client_id',36)->notNullable();
         $table->string('menuNo',2)->notNnullable();
         $table->timestamps();
         $table->unique(['parent_id', 'client_id'],'parentClient');
         $table->unique(['parent_id', 'menuNo'],'parentSubMenu');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('aggregated_clients');
   }
};
