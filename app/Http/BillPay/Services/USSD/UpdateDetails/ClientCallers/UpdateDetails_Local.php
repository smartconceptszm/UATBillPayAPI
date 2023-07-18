<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\BillPay\Services\CRM\CRMService;
use Exception;

class UpdateDetails_Local implements IUpdateDetailsClient
{

   private $customerUpdateService;
   public function __construct(CRMService $customerUpdateService)
   {
      $this->customerUpdateService = $customerUpdateService;
   }

   public function create(array $customerDetailsData):string
   {
      
      try{
         return $this->customerUpdateService->create($customerDetailsData);
      } catch (\Throwable $e) {
         throw new Exception('At Post customer update details. '.$e->getMessage());
      }                                             

   }

}