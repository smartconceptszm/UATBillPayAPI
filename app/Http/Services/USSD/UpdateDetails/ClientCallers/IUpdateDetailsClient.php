<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

interface IUpdateDetailsClient 
{
   public function create(array $detailsData): string;
}