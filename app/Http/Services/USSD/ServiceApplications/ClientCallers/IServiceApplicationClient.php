<?php

namespace App\Http\Services\USSD\ServiceApplications\ClientCallers;

interface IServiceApplicationClient 
{
   public function create(array $serviceApplicationData): string;
}