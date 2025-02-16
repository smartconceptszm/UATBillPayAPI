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

      Schema::create('client_customers', function (Blueprint $table) {
            $table->id();
            $table->string("client_id",36)->notNullable();
            $table->string("customerAccount",36)->notNullable();
            $table->string('revenuePoint',150)->notNullable();
            $table->string('consumerTier',150)->notNullable();
            $table->string('consumerType',150)->notNullable();
            $table->timestamps();
            $table->unique(['client_id', 'customerAccount'],'client_customerAccount');
            $table->index(['created_at']);
         });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_revenue_points');
   }
};
