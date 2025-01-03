<?php

namespace App\Http\Services\CRM;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MainComplaintsDashboardService
{

   public function findAll(array $criteria)
   {
      
      try {
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m-d',$dto->theMonth.'-01');
         $startOfMonth = $theDate->copy()->startOfMonth();
         $endOfMonth = $theDate->copy()->endOfMonth();
         //Get all in Date Range
            $theComplaints = DB::table('complaints as c')
                                 ->join('complaint_sub_types as cst','c.complaint_subtype_id','=','cst.id')
                                 ->join('complaint_types as ct','cst.complaint_type_id','=','ct.id')
                                 ->join('clients as clt','c.client_id','=','clt.id')
                                 ->select(DB::raw("clt.id,clt.urlPrefix,clt.name, 
                                                      CONCAT(ct.name, ' - ', cst.name) as complaint,
                                                      COUNT(c.id) AS totalComplaints"))
                                 ->whereBetween('c.created_at', [$startOfMonth,$endOfMonth])
                                 ->groupBy('clt.id','clt.urlPrefix','clt.name')
                                 ->groupBy('complaint')
                                 ->orderBy('totalComplaints','desc')
                                 ->get();
            if($theComplaints->isNotEmpty()){
               $complaintsByClient = $theComplaints->groupBy('urlPrefix');
               $complaintsSummary = $complaintsByClient->map(function ($client){
                  $clientDetails = $client->get(0);   
                  $formattedData = $client->map(function ($item) {
                     return [
                                 'complaint'=>$item->complaint,
                                 'totalComplaints'=>$item->totalComplaints
                              ];
                           });
                  $totalComplaints = $client->reduce(function ($complaints, $item) {
                                                      return $complaints + $item->totalComplaints;
                                                   }); 
                  $formattedData->prepend([
                        'complaint'=>'TOTAL',
                        'totalComplaints'=>$totalComplaints
                     ]);
                  
                  return [
                           'urlPrefix'=>$clientDetails->urlPrefix,
                           'name'=>$clientDetails->name,
                           'id'=>$clientDetails->id,
                           'data'=>$formattedData->toArray()
                        ];
               });
               return $complaintsSummary;
            }else{
               return [];
            }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
