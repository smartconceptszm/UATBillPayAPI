<?php

namespace App\Http\Services\USSD\Survey;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey_Step_1
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
         $txDTO->error = 'Survey step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;

   }

}