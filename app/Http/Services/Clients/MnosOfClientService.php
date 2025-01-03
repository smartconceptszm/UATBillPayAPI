<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\DB;
use Exception;

class MnosOfClientService
{

   public function findAll(string $client_id):array|null{

      try {
         $records = DB::table('client_mnos as cmno')
                        ->join('clients as c','cmno.client_id','=','c.id')
                        ->join('mnos as m','cmno.mno_id','=','m.id')
                        ->join('client_sms_channels as csc','cmno.smsChannel','=','csc.id')
                        ->join('sms_providers as sp','csc.sms_provider_id','=','sp.id')
                        ->select('cmno.*','sp.id as sms_provider_id','csc.id as smsChannel',
                                    'sp.name as smsChannelName','sp.handler','m.name as mno','c.name as client')
                        ->where('cmno.client_id', '=', $client_id);                        
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}
