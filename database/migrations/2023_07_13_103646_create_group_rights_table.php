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
      Schema::create('group_rights', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("group_id")->notNullable();
         $table->unsignedBigInteger("right_id")->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('group_rights');
   }
};
