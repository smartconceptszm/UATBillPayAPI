<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Clients\ClientRevenueCodeService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetAmount 
{

   public function __construct(
      private ClientWalletCredentialsService $walletCredentialsService,
      private ClientRevenueCodeService $clientRevenueCodeService,
      private ClientWalletService $clientWalletService)
   {}

   public function handle(BaseDTO $txDTO):array
   {
      
      $subscriberInput = \str_replace("ZMW", "", $txDTO->subscriberInput);
      $subscriberInput = \str_replace("ZMK", "", $subscriberInput);
      $subscriberInput = \str_replace(" ", "", $subscriberInput);
      $subscriberInput = \str_replace("K", "",$subscriberInput);
      $subscriberInput = \str_replace(",", "",$subscriberInput);
      $amount = (float)$subscriberInput;
      $subscriberInput = number_format($amount, 2, '.', ',');
      
      if(!$txDTO->wallet_id){
         $theWallet = $this->clientWalletService->findOneBy([
                                                      'payments_provider_id'=> $txDTO->payments_provider_id,
                                                      'client_id'=>$txDTO->client_id                                              
                                                   ]);
         $txDTO->wallet_id = $theWallet->id;  
      }
      $walletCredentials = $this->walletCredentialsService->getWalletCredentials($txDTO->wallet_id);
      $maxPaymentAmount = (float)$walletCredentials['MAX_PAYMENT_AMOUNT'];
      $minPaymentAmount = (float)$walletCredentials['MIN_PAYMENT_AMOUNT'];

      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $testMSISDN = \explode("*", $billpaySettings['APP_ADMIN_MSISDN']."*".$txDTO->testMSISDN);
      $testMSISDN = array_filter($testMSISDN,function($entry){
                                                return $entry !== "";
                                             });
      if ((($amount< $minPaymentAmount) || $amount > $maxPaymentAmount) && !(\in_array($txDTO->mobileNumber, $testMSISDN))) {
         throw new Exception("Amount either below the minimum or above the maximum amount allowed", 1);
      }

      $revenueCode = $this->clientRevenueCodeService->findOneBy(['menu_id'=>$txDTO->menu_id,'code'=>$txDTO->customerAccount]);
      if($revenueCode){
         if(($revenueCode->amountFixed) =='YES' && ($amount != $revenueCode->requiredAmount)){
            throw new Exception("Payment amount MUST equal the required amount of ZMW ".
            number_format($revenueCode->requiredAmount, 2, '.', ','), 1);
         }
      }
      return [$subscriberInput, $amount];

   }
    
}
