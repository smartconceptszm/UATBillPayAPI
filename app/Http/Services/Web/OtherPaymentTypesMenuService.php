<?php

namespace App\Http\Services\Web;

use App\Http\Services\Clients\OtherPaymentTypeService;
use App\Http\Services\Clients\ClientService;
use Exception;

class OtherPaymentTypesMenuService
{

   public function __construct(
      private OtherPaymentTypeService $otherPaymentTypeService,
      private ClientService $clientService,
   )
   {}

   public function findAll(array $criteria = null):array|null
   {

      try {
         $client = $this->clientService->findOneBy($criteria );
         return $this->otherPaymentTypeService->findAll([
                              'client_id' =>  $client->id,
                              'isActive' =>  "YES",
                           ]);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
