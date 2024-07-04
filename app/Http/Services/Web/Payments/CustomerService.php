<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\DTOs\MoMoDTO;
use Exception;

class CustomerService
{

   public function __construct(
      private IEnquiryHandler $enquiryHandler,
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
