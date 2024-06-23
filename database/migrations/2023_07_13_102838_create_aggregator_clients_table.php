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
         $table->string('parent_client_id',36)->notNullable();
         $table->string('menuNo',2)->notNnullable();
         $table->string('urlPrefix',25)->unique()->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('clients');
   }
};
