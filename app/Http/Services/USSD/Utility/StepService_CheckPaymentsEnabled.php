<?php

namespace App\Http\Services\USSD\Utility;

use App\Http\Services\Clients\ClientMnoService;
use App\Http\DTOs\BaseDTO;

class StepService_CheckPaymentsEnabled 
{

   public function __construct(
      private ClientMnoService $momoService)
   {}

   public function handle(BaseDTO $txDTO):array
   {

      $response = [
                  'enabled'=>true,
                  'responseText' => ""
               ];

      $mnoMoMo = $this->momoService->findOneBy([
                        'client_id' => $txDTO->client_id,
                        'mno_id' => $txDTO->mno_id,             
                     ]);
      $mnoMoMo = (object)$mnoMoMo->toArray();
      if ($mnoMoMo->momoActive != 'YES'){
         $response['responseText'] = "Payments to ".\strtoupper($txDTO->urlPrefix).
                  " via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
         $response['enabled'] = false;
         return $response;
      }

      if($mnoMoMo->momoMode == 'DOWN'){
         $response['responseText'] = $mnoMoMo->modeMessage;
         $response['enabled'] = false;
         return $response;
      }

      if (\env('APP_ENV') != 'production'){
         $testMSISDN = \explode("*", \env('APP_ADMIN_MSISDN')."*".$txDTO->testMSISDN);
         if (!\in_array($txDTO->mobileNumber, $testMSISDN)){
            $response['responseText'] = "Payments to ".\strtoupper($txDTO->urlPrefix).
                  " via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
            $response['enabled'] = false;
         }
      }

      return $response;
      
   }
    
}
