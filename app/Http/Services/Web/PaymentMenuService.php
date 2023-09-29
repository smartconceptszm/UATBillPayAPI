<?php

namespace App\Http\Services\Web;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use Exception;

class PaymentMenuService
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private ClientService $clientService
   )
   {}

   public function findAll(array $criteria = null):array|null
   {

      try {
         $client = $this->clientService->findOneBy($criteria );
         return $this->clientMenuService->findAll([
                              'client_id' =>  $client->id,
                              'isPayment' =>  "YES",
                           ]);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
