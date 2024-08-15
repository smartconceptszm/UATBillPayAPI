<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentSpoofer_Step_1 
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    

         $rootMenu = $this->clientMenuService->findOneBy([
                                 'client_id' => $txDTO->client_id,
                                 'parent_id' =>'0'
                              ]);
         $menus = $this->clientMenuService->findAll([
                                 'client_id'=>$txDTO->client_id,
                                 'parent_id'=>$rootMenu->id,
                                 'isActive' => 'YES'
                              ]);
         $prompt = $rootMenu->prompt."\n";
         foreach ($menus as $menu) {
            $prompt .= $menu->order.". ".$menu->prompt."\n";
         }
         $prompt .= "\n";
         $txDTO->response = $prompt;
         
      } catch (\Throwable $e) {
         $txDTO->error = 'Council proxy payment step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
   
}