<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Clients\ClientService;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentsProviderService
{

   public function __construct(
      private ClientService $clientService
   )
   {}

   public function findAll(array $criteria):array|null
   {

      try {
         $client = $this->clientService->findOneBy($criteria);
         $client = \is_null($client)?null:(object)$client->toArray();
         $records = DB::table('client_wallets as cw')
                  ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                  ->select('cw.*','pp.shortName','pp.name','pp.colour',)
                  ->where('cw.client_id', '=', $client->id)
                  ->where('cw.paymentsActive', '=', 'YES')
                  ->where('cw.paymentsMode', '=', 'UP')
                  ->get()->all();
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
