<?php

namespace App\Http\BillPay\Services\USSD\Survey\ClientCallers;

interface ISurveyClient 
{
   public function create(array $detailsData): string;
}