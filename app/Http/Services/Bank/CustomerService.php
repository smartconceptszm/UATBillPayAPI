<?php

namespace App\Http\Services\Bank;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Exceptions\ApiException;
use App\Http\DTOs\MoMoDTO;
use Exception;

class CustomerService
{

   public function __construct(
      private EnquiryHandler $enquiryHandler,
      private MoMoDTO $momoDTO,
   )
   {}

   public function getCustomer(array $data):array
   {

      try {
         $paymentDTO = $this->momoDTO->fromArray($data);
         $paymentDTO = $this->enquiryHandler->handle($paymentDTO);
         return $paymentDTO->customer;
      } catch (\Throwable $e) {
         switch ($e->getCode()) {
            case 1:
               throw new ApiException($e->getMessage(),404);
               break;
            case 2:
               throw new ApiException("Internal error, could not connect to ".strtoupper($paymentDTO->urlPrefix),500);
               break;
            default:
               throw new ApiException($e->getMessage());
               break;
         }
         
      }

   }

}