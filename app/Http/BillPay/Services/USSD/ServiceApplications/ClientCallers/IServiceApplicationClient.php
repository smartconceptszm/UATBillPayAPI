<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers;

interface IServiceApplicationClient 
{
   public function create(array $serviceApplicationData): string;
}