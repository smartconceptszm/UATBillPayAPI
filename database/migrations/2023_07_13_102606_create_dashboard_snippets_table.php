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
         $table->id();
         $table->string('name',150)->notNullable();
         $table->string('title',150)->notNullable();
         $table->enum('type',['LINE', 'BAR', 'DOUGHNUT', 'CALLOUT', 'PIE','POLAR','RADAR'])->notNullable();
         $table->string('generateHandler',50)->notNullable();
         $table->string('viewHandler',50)->notNullable();
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
