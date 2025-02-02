<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Clients\PaymentsProviderService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class ConfirmToPay
{

   public function __construct(
      private PaymentsProviderService $paymentsProviderService,
      private ClientWalletService $clientWalletService)
   {}

    public function handle(BaseDTO $txDTO):BaseDTO
    {

      try {
         if ($txDTO->subscriberInput == '1') {
            $paymentsProvider = $this->paymentsProviderService->findById($txDTO->payments_provider_id);
            $clientWallet = $this->clientWalletService->findOneBy([
                                          'payments_provider_id' => $txDTO->payments_provider_id, 
                                          'client_id' => $txDTO->client_id,
                                       ]);
            $txDTO->response = \strtoupper($txDTO->urlPrefix).
                                    " Payment request submitted to ".$paymentsProvider->name." \n".
                                    "You will receive a PIN prompt shortly!"."\n\n";
            $txDTO->walletHandler = $clientWallet->handler;
            $txDTO->wallet_id = $clientWallet->id;
            $txDTO->fireMoMoRequest = true;
            $txDTO->status = USSDStatusEnum::Completed->value;
            $txDTO->lastResponse = true;
         } else {
            if (\strlen($txDTO->subscriberInput) > 1) {
               throw new Exception("Customer most likely put in PIN instead of '1' to confirm", 1);
            }
            throw new Exception("Invalid confirmation", 1);
         }
      } catch (\Throwable $e) {

         if($e->getCode()==1){
            $txDTO->errorType = USSDStatusEnum::InvalidConfirmation->value;
         }else{
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
         $txDTO->error = $e->getMessage();
         
      }
      
      return $txDTO;
        
    }

    
    
}