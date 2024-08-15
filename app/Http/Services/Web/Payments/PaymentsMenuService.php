<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Clients\ClientService;
use Exception;

class PaymentsMenuService
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private ClientService $clientService
   )
   {}

   public function findAll(array $criteria):array|null
   {

      try {
         $client = $this->clientService->findOneBy($criteria);
         return $this->clientMenuService->findAll([
                              'client_id' =>  $client->id,
                              'isPayment' =>  "YES"
                           ]);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
