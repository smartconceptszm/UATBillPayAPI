<?php

namespace App\Http\Services\Auth;


use Illuminate\Support\Facades\DB;
use Exception;

class APIUsersOfAPIClientService
{

   public function findAll(?array $criteria):array|null
   {

      try {
         
            $dto = (object)$criteria;
            $records = DB::table('api_users as au')
                        ->join('api_clients as ac','au.api_client_id','=','ac.id')
                        ->join('clients as sp','au.service_provider_id','=','sp.id')
                        ->join('payments_providers as pp','au.payments_provider_id','=','pp.id')
                        ->where('au.api_client_id', '=', $dto->api_client_id)
                        ->select('au.id','au.username','au.service_provider_id','sp.name as ServiceProvider',
                                    'ac.category','au.payments_provider_id','pp.name as PaymentsProvider','au.status')
                        ->get();
            return $records->all();

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
