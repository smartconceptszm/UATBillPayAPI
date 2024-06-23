<?php

namespace App\Http\Services\USSD\Utility;

use App\Http\Services\Web\Clients\PaymentsProviderService;
use App\Http\Services\Web\Clients\ClientWalletService;
use App\Http\DTOs\BaseDTO;

class StepService_CheckPaymentsEnabled 
{

   public function __construct(
      private PaymentsProviderService $paymentsProviderService,
      private ClientWalletService $ClientWalletService)
   {}

   public function handle(BaseDTO $txDTO):array
   {

      $response = [
                  'enabled'=>true,
                  'responseText' => ""
               ];

      $clientWallet = $this->ClientWalletService->findOneBy([
                                    'payments_provider_id' => $txDTO->payments_provider_id, 
                                    'client_id' => $txDTO->client_id,
                                 ]);
      $clientWallet = \is_null($clientWallet)?null: (object)$clientWallet->toArray();
      if ($clientWallet){
         if ($clientWallet->paymentsActive != 'YES'){
            $response['enabled'] = false;
         }
      }else{
         $response['enabled'] = false;
      }

      if(!$response['enabled']){
         $paymentProvider = $this->paymentsProviderService->findById($txDTO->payments_provider_id);
         $response['responseText'] = "Payments to ".\strtoupper($txDTO->urlPrefix).
                  " via " . $paymentProvider->name . " will be launched soon!" . "\n" .
                  "Thank you for your patience.";
         return $response;
      }

      if($clientWallet->paymentsMode == 'DOWN'){
         $response['responseText'] = $clientWallet->modeMessage;
         $response['enabled'] = false;
         return $response;
      }

      return $response;
      
   }
    
}
