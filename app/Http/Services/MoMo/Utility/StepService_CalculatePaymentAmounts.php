<?php

namespace App\Http\Services\MoMo\Utility;

use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Clients\ClientService;

class StepService_CalculatePaymentAmounts
{

   public function __construct(
      private ClientService $clientService,
      private ClientMnoService $ClientMnoService)
   {}

   public function handle(string $urlPrefix, string $mno_id, string $paymentAmount):array
   {

      $paymentAmount = (float)(\str_replace(",", "",$paymentAmount));
      $receiptAmount = $paymentAmount;
      $surchargeAmount = 0;
      $client = $this->clientService->findOneBy(['urlPrefix'=>$urlPrefix]); 
      if($client->surcharge=='YES'){
         $ClientMno = $this->ClientMnoService->findOneBy([
                     'client_id'=>$client->id,
                     'mno_id'=>$mno_id
                  ]);
         //Apply Tariff Here
         $surchargeAmount =  ($paymentAmount*0.01)*((100-(float)$ClientMno->momoCommission)/100);
         $paymentAmount += $paymentAmount *0.01;
      }
      return [
            'paymentAmount'=>$paymentAmount,
            'receiptAmount'=>$receiptAmount,
            'surchargeAmount'=>$surchargeAmount,
            'clientCode'=>$client->code
         ];
   }

}