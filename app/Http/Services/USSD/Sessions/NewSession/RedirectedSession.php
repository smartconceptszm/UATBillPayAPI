<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionService;
use App\Http\DTOs\BaseDTO;

class RedirectedSession extends EfectivoPipelineContract
{

   public function __construct(
      private SessionService $sessionService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if($txDTO->isNewRequest != '1'){
            
            $ussdSession = $this->sessionService->findOneBy([   
                                                      'mobileNumber'=>$txDTO->mobileNumber,
                                                      'sessionId'=>$txDTO->sessionId,
                                                   ]);
            if(!$ussdSession){
               $txDTO->subscriberInput = "115";  //$txDTO->shortCode;
               $txDTO->isNewRequest = '1';
            }

         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}