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

      if ($mnoMoMo->momoActive != 'YES'){
         $response['responseText'] = "Payment for ".\strtoupper($txDTO->urlPrefix).
                  " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
         $response['enabled'] = false;
      }

      if($mnoMoMo->momoMode == 'DOWN'){
         $response['responseText'] = $mnoMoMo->modeMessage;
         $response['enabled'] = false;
      }

      if (\env('APP_ENV') != 'Production'){
         $testMSISDN = \explode("*", 
                              \env('APP_TEST_MSISDN')."*".
                              $txDTO->testMSISDN);
         if (!\in_array($txDTO->mobileNumber, $testMSISDN)){
            $response['responseText'] = "Payment for ".\strtoupper($txDTO->urlPrefix).
                  " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
            $response['enabled'] = false;
         }
      }

      return $response;
      
   }
    
}
