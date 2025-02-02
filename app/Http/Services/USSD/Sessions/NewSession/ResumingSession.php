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

         if($txDTO->isNewRequest == '1' && (\count(\explode("*", $txDTO->subscriberInput))==1)){
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $strSettingKey = "RESUME_PAYMENT_SESSION_ENABLED_".\strtoupper($txDTO->urlPrefix);
            if($billpaySettings[$strSettingKey]=='YES'){
               $sessionToResume =  $this->sessionHistoryService->getLatestIncompletePayment($txDTO);
               if($sessionToResume){
                  Cache::put($txDTO->sessionId.'_Resume', json_encode(get_object_vars($sessionToResume)));
                  $txDTO->billingClient = $sessionToResume->billingClient; 
                  $txDTO->menu_id = $sessionToResume->menu_id; 
                  $txDTO->menuPrompt = $txDTO->menuPrompt.".\n".$billpaySettings['RESUME_PAYMENT_SESSION_PROMPT'];
                  $txDTO->handler = $billpaySettings['RESUME_PAYMENT_SESSION_HANDLER']; 
               }
            }
         }
      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}