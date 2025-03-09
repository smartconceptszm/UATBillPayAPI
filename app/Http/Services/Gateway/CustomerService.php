<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\DTOs\MoMoDTO;
use Exception;

class CustomerService
{

   public function __construct(
      private EnquiryHandler $enquiryHandler,
      private MoMoDTO $momoDTO,
   )
   {}

   public function getCustomer(array $criteria):array
   {

      try {
         $paymentDTO = $this->momoDTO->fromArray($criteria);
         $paymentDTO = $this->enquiryHandler->handle($paymentDTO);
         return $paymentDTO->customer;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}