<?php

namespace App\Http\Services\MoMo\Utility;

use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Clients\ClientService;
use App\Http\DTOs\BaseDTO;

class StepService_CalculatePaymentAmounts
{

   public function __construct(
      private ClientService $clientService,
      private ClientMnoService $ClientMnoService)
   {}

   public function handle(BaseDTO $txDTO):array
   {

      $paymentAmount = (float)(\str_replace(",", "",$txDTO->paymentAmount));
      $receiptAmount = $paymentAmount;
      $surchargeAmount = 0;
      if($txDTO->clientSurcharge=='YES'){
         $ClientMno = $this->ClientMnoService->findOneBy([
                     'client_id'=>$txDTO->client_id,
                     'mno_id'=>$txDTO->mno_id
                  ]);
         //Apply Tariff Here
         $surchargeAmount =  ($paymentAmount*0.01)*((100-(float)$ClientMno->momoCommission)/100);
         $paymentAmount += $paymentAmount *0.01;
      }
      return [
            'paymentAmount'=>$paymentAmount,
            'receiptAmount'=>$receiptAmount,
            'surchargeAmount'=>$surchargeAmount
         ];
   }

}