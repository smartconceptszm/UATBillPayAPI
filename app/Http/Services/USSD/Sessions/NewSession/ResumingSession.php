<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionHistoryService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Exception;

class ResumingSession extends EfectivoPipelineContract
{

   public function __construct(
      private SessionHistoryService $sessionHistoryService,
      private ClientMenuService $clientMenuService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if($txDTO->isNewRequest == '1'){
            $resumeSessionMenu = $this->clientMenuService->findOneBy([
                                                               'handler' => 'ResumePreviousSession',
                                                               'client_id' => $txDTO->client_id,
                                                               'parent_id' =>$txDTO->menu_id
                                                            ]);
            if($resumeSessionMenu){
               $sessionToResume =  $this->sessionHistoryService->getLatestIncompletePayment($txDTO);
               if($sessionToResume){
                  Cache::put($txDTO->sessionId.'_Resume', json_encode(get_object_vars($sessionToResume)));
                  $txDTO->billingClient = $resumeSessionMenu->billingClient; 
                  $txDTO->menuPrompt = $resumeSessionMenu->prompt;
                  $txDTO->handler = $resumeSessionMenu->handler; 
                  $txDTO->menu_id = $resumeSessionMenu->id; 
               }
            }
         }
      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}