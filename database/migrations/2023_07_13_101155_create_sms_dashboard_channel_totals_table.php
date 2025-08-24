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
      Schema::create('sms_dashboard_channel_totals', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->string('channel',50)->notNullable();
         $table->date('dateOfMessage');
         $table->unsignedInteger('year')->notNullable();
         $table->unsignedInteger('month')->notNullable();
         $table->unsignedInteger('day',2)->notNullable();
         $table->unsignedInteger('numberOfMessages')->default(0);
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','channel','dateOfMessage'],'client_channel_day');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('sms_dashboard_channel_totals');
   }
};
