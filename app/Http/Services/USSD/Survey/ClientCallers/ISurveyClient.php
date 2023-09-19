<?php

namespace App\Http\Services\USSD\Survey\ClientCallers;

interface ISurveyClient 
{
   public function create(array $detailsData): string;
}