<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Illuminate\Support\Carbon;


class InvalidAccount implements IErrorResponse
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {    
         // $txDTO->response = "Invalid ".\strtoupper($txDTO->urlPrefix).
         //                       " account/meter number.";
         // $txDTO->lastResponse = true;
         $txDTO->response = "Invalid ".\strtoupper($txDTO->urlPrefix).
                              " account/meter number.\n\n<<Enter 0 to go back>>\n";

         if($txDTO->isPayment == 'YES'){
            $theSteps=2;
         }else{
            $theSteps=1;
         }
         $cacheValue = \json_encode([
                              'must'=>true,
                              'steps'=>$theSteps,
                           ]);
         Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
                              Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
      } catch (\Throwable $e) {
         $txDTO->error = 'At Generate invalid account response. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}