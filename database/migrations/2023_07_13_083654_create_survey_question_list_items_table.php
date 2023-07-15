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
        Schema::create('survey_question_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_question_id')->notNullable();
            $table->string('prompt',155)->notNullable();
            $table->unsignedTinyInteger('order')->notNullable();
            $table->timestamps();
         });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_question_list_items');
    }
};
