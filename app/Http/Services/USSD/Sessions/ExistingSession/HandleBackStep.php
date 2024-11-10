<?php

namespace App\Http\Services\USSD\Sessions\ExistingSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class HandleBackStep extends EfectivoPipelineContract
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $handleBack = \json_decode(cache($txDTO->sessionId."handleBack",''),true);

         if($handleBack){

            cache()->forget($txDTO->sessionId."handleBack");

            if($txDTO->subscriberInput ==='0' || $handleBack['must']){

               $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
               $backSteps = $handleBack['steps'];
               $txDTO->status = 'INITIATED';
               for ($i=1; $i <= $backSteps; $i++) { 
                  if($arrCustomerJourney){
                     \array_pop($arrCustomerJourney);
                  }
               }

               // if(!cache($txDTO->sessionId."responseNext",'')){
               if($txDTO->handler != 'NextPage'){
                  if( \count($arrCustomerJourney) > 1){
                     $txDTO->subscriberInput = \end($arrCustomerJourney);
                     \array_pop($arrCustomerJourney);
                     if(\count($arrCustomerJourney)==1){
                        $txDTO->customerJourney =$arrCustomerJourney[0];
                     }else{
                        $txDTO->customerJourney =\implode("*", $arrCustomerJourney);
                     }
                  }else{
                     $selectedMenu = $this->clientMenuService->findOneBy([
                                                      'client_id' => $txDTO->client_id,
                                                      'parent_id' => 0
                                                   ]);
                     $txDTO->billingClient = $selectedMenu->billingClient; 
                     $txDTO->menuPrompt = $selectedMenu->prompt;
                     $txDTO->handler = $selectedMenu->handler; 
                     $txDTO->menu_id = $selectedMenu->id;
                     $txDTO->customerJourney = '';
                  }
               }

               cache()->forget($txDTO->sessionId."responseNext");

            }

         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}