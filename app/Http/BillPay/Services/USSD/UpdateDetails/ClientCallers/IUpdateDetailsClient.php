<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers;

interface IUpdateDetailsClient 
{
   public function create(array $detailsData): string;
}