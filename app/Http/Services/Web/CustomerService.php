<?php

namespace App\Http\Services\Web;

use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\DTOs\MoMoDTO;
use Exception;

class CustomerService
{

   public function __construct(
      private IEnquiryHandler $enquiryHandler,
      private MoMoDTO $moMoDTO,
   )
   {}

   public function getCustomer(array $criteria):array
   {

      try {
         $momoDTO = $this->moMoDTO->fromArray($criteria);
         $momoDTO = $this->enquiryHandler->handle($momoDTO);
         return $momoDTO->customer;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
