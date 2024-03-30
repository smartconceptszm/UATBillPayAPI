<?php

namespace App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers;

use App\Http\DTOs\BaseDTO;

interface IEnquiryHandler 
{
   public function handle(BaseDTO $txDTO):BaseDTO;
}