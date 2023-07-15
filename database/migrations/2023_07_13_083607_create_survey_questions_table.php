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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id')->notNullable();
            $table->string('prompt',150)->notNullable();
            $table->enum('type',['MOBILE','LIST','NATIONALID','ONEWORD','GENERAL'])->default('GENERAL')->notNullable();
            $table->unsignedTinyInteger('order')->notNullable();
            $table->timestamps();
         });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
