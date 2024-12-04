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
      Schema::create('sms_channels_credentials', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("channel_id",36)->notNullable();
         $table->string("key",50)->notNullable();
         $table->string("keyValue",150)->notNullable();
         $table->string("description",150)->nullable();
         $table->timestamps();
         $table->unique(['channel_id','key'],'clientKey');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('sms_channels_credentials');
   }
};
