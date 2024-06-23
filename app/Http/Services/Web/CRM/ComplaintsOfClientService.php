<?php

namespace App\Http\Services\Web\CRM;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Carbon;
use Exception;

class ComplaintsOfClientService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('complaints as c')
            // ->join('sessions as s','c.session_id','=','s.id')
            // ->join('mnos as m','s.mno_id','=','m.id')
            ->join('complaint_sub_types as cst','c.complaint_subtype_id','=','cst.id')
            ->join('complaint_types as ct','cst.complaint_type_id','=','ct.id')
            ->leftJoin('users as u1','c.assignedBy','=','u1.id')
            ->leftJoin('users as u2','c.assignedTo','=','u2.id')
            ->select('c.*','cst.code as subTypeCode','cst.name as subType', 'ct.code as complaintCode',
                     'ct.name as complaintType', 
                     )
            // ->where('s.menu', '=', 'FaultsComplaints')
            ->where('c.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereBetween('c.created_at', [$dto->dateFrom, $dto->dateTo]);
         }
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
