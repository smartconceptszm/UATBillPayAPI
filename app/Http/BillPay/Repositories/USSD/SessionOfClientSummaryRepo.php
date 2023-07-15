<?php

namespace App\Http\BillPay\Repositories\USSD;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class SessionOfClientSummaryRepo implements IFindAllRepository
{

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $dto=(object)$criteria;
         $records = DB::table('sessions as s')
                  ->join('mnos as m','m.id','=','s.mno_id')
                  ->select(DB::raw('count(s.id) as requestsCount, 
                                 s.menu as service,m.name as mno'))
                  ->where('s.client_id', '=', $dto->client_id);
         if($dto->from && $dto->to){
               $records =$records->whereDate('s.created_at', '>=', $dto->from)
                                 ->whereDate('s.created_at', '<=', $dto->to);
         }
         $records = $records->groupBy('service', 'mno')
                              ->orderBy('mno', 'asc')
                              ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
