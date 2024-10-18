<?php

namespace App\Http\Services\USSD\GetLastToken;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetLastToken_Step_1
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    

			$clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
			$txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
         
      } catch (\Throwable $e) {

         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'WalletNotActivated';
         }else{
            $txDTO->error = 'Buy units sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }

      }
      return $txDTO;
      
   }
   
}