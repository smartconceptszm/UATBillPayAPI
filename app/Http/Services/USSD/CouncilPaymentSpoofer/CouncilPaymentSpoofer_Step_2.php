<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\USSD\StepServices\GetSpoofedMenu;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentSpoofer_Step_2
{

   public function __construct(
      private GetSpoofedMenu $getSpoofedMenu,
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $selectedMenu = $this->getSpoofedMenu->handle($txDTO);
         if(!$selectedMenu){
            throw new Exception("Invalid Menu Item number", 1);
         }
         $txDTO->response="Enter the mobile money number for the customer (e.g 09xx xxxxxx/07xx xxxxxx)\n";
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council proxy payment step 2. '.$e->getMessage();
      }
      return $txDTO;
      
   }
   
}