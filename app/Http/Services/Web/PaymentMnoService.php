<?php

namespace App\Http\Services\Web;

use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentMnoService
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
         $records = DB::table('client_mnos as cm')
                  ->join('mnos as m','cm.mno_id','=','m.id')
                  ->select('cm.*','m.name','m.colour',)
                  ->where('cm.client_id', '=', $client->id)
                  ->where('cm.momoActive', '=', 'YES')
                  ->where('cm.momoMode', '=', 'UP')
                  ->get()->all();
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
