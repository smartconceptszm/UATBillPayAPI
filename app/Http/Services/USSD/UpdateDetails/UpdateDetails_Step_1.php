<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_Step_1
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
         $txDTO->error = 'Update details step 1. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;

   }

}