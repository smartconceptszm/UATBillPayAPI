<?php

namespace App\Http\BillPay\Services\MoMo\Utility;

use App\Http\BillPay\Services\ClientMnoService;
use App\Http\BillPay\Services\ClientService;

class StepService_CalculatePaymentAmounts
{

   private $ClientMnoService;
   private $clientService;
   public function __construct(ClientService $clientService,
      ClientMnoService $ClientMnoService)
   {
      $this->ClientMnoService = $ClientMnoService;
      $this->clientService = $clientService;
   }

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