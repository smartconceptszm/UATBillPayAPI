<?php

namespace App\Http\BillPay\Repositories\USSD;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class SessionOfClientRepo implements IFindAllRepository
{

   private $table = 'sessions';

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $dto=(object)$criteria;
         $records = DB::table($this->table.' as s')
                  ->select('*')
                  ->where('s.client_id', '=', $dto->client_id);
         if($dto->from && $dto->to){
               $records =$records->whereDate('s.created_at', '>=', $dto->from)
                                 ->whereDate('s.created_at', '<=', $dto->to);
         }
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
