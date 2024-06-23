<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Web\Clients\ClientWalletService;
use App\Http\DTOs\BaseDTO;

class StepService_CalculatePaymentAmounts
{

   public function __construct(
      private ClientWalletService $ClientWalletService
      )
   {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      $clientWallet = $this->ClientWalletService->findById($txDTO->wallet_id);
      $paymentAmount = (float)(\str_replace(",", "",$txDTO->paymentAmount));
      $receiptAmount = $paymentAmount;
      $surchargeAmount = 0;
      if($txDTO->clientSurcharge=='YES'){
         //Apply Tariff Here
         $surchargeAmount =  ($paymentAmount*0.01)*((100-(float)$clientWallet->paymentsCommission)/100);
         $paymentAmount += $paymentAmount *0.01;
      }
      $txDTO->surchargeAmount=$surchargeAmount;
      $txDTO->paymentAmount=$paymentAmount;
      $txDTO->receiptAmount=$receiptAmount;
      return $txDTO;
      
   }

}