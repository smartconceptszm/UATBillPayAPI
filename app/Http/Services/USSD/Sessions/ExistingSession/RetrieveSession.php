<?php

namespace App\Http\Services\USSD\Sessions\ExistingSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class RetrieveSession extends EfectivoPipelineContract
{

   public function __construct(
      private SessionService $sessionService,
      private ClientService $clientService)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $ussdSession = $this->sessionService->findOneBy([   
                                                         'mobileNumber'=>$txDTO->mobileNumber,
                                                         'sessionId'=>$txDTO->sessionId,
                                                      ]);
                                                      
         $this->checkForAggregation($txDTO,$ussdSession);

         $txDTO->customerJourney = $ussdSession->customerJourney;
         $txDTO->customerAccount = $ussdSession->customerAccount;
         $txDTO->paymentAmount = $ussdSession->paymentAmount;
         $txDTO->revenuePoint = $ussdSession->revenuePoint;
         $txDTO->created_at = $ussdSession->created_at;

         $txDTO->menu_id = $ussdSession->menu_id;
         $txDTO->mno_id = $ussdSession->mno_id;
         $txDTO->status = $ussdSession->status;
         $txDTO->error = $ussdSession->error;
         $txDTO->id = $ussdSession->id;
         if($this->isSessionStatusTerminalError($txDTO->status)){
            $txDTO->errorType = $ussdSession->status;
         }else{
            $txDTO->errorType = "";
            $txDTO->error = '';
         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

   private function isSessionStatusTerminalError(string $status): bool
   {
      return in_array($status, [
                                 USSDStatusEnum::ClientBlocked->value,
                                 USSDStatusEnum::InvalidAccount->value,
                                 USSDStatusEnum::InvalidAmount->value,
                                 USSDStatusEnum::InvalidInput->value,
                                 USSDStatusEnum::MaintenanceMode->value,
                                 USSDStatusEnum::SystemError->value,
                                 USSDStatusEnum::WalletNotActivated->value
                              ]);
   }

   private function checkForAggregation(BaseDTO $txDTO, $ussdSession) : BaseDTO {
      if($txDTO->ussdAggregator == 'YES'){
         $client = $this->clientService->findById($ussdSession->client_id);
         $txDTO->ussdAggregator = $client->ussdAggregator; 
         $txDTO->clientSurcharge = $client->surcharge; 
         $txDTO->testMSISDN = $client->testMSISDN;
         $txDTO->shortCode = $client->shortCode;
         $txDTO->urlPrefix = $client->urlPrefix;
         $txDTO->client_id = $client->id;
      }
      return $txDTO;
   }

}