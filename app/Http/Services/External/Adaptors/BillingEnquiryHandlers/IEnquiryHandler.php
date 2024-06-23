<?php

namespace App\Http\Services\External\Adaptors\BillingEnquiryHandlers;

use App\Http\DTOs\BaseDTO;

interface IEnquiryHandler 
{
   public function handle(BaseDTO $txDTO):BaseDTO;
}