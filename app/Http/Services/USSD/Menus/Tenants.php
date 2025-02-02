<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Clients\ClientService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;

class Tenants implements IUSSDMenu
{
   public function __construct(
      private ClientService $clientService)
   {}
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {
         if($txDTO->error==''){
            $clients = $this->clientService->findAll();
            $menus = [];
            foreach ($clients as $client) {
               $menus[]= $client->shortCode." - ".$client->shortName;
            }
            \sort($menus);
            $prompt = "Platform Short Codes:\n";
            foreach ($menus as $menu) {
               $prompt .= $menu."\n";
            }
            $prompt .= "\n";
            $txDTO->response = $prompt;
         }
      } catch (\Throwable $e) {
         $txDTO->error='At handle tenant menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }

      return $txDTO;
   }

}