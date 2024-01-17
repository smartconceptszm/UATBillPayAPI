<?php

namespace App\Http\Services\CRM;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ComplaintDashboardService
{

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         //Get all in Date Range
            $theComplaints = DB::table('complaints as c')
               ->join('complaint_sub_types as cst','c.complaint_subtype_id','=','cst.id')
               ->join('complaint_types as ct','cst.complaint_type_id','=','ct.id')
               ->select('c.*','cst.code as subTypeCode','cst.name as subTypeName',
                           'ct.code as typeCode','ct.name as typeName')
               ->where('c.client_id', '=', $dto->client_id)
               ->whereDate('c.created_at', '>=', $dto->dateFrom)
               ->whereDate('c.created_at', '<=', $dto->dateTo)
               ->get();

            $groupedData = $theComplaints->groupBy('typeName');
            $byType =[];
            foreach ($groupedData as $key => $value) {
               $byType[] = [
                     "type"=>$key,
                     "totalComplaints" => $value->count('id')
                  ];
            }

            $groupedData = $theComplaints->groupBy('district');
            $byDistrict=[];
            foreach ($groupedData as $key => $value) {
               $byDistrict[]= [
                     "district"=>$key,
                     "totalComplaints"=>$value->count('id')
                  ];
            }
         // Get all Days in Current Month
            $theDateTo = Carbon::parse($dto->dateTo);
            $currentYear = $theDateTo->format('Y');
            $currentMonth = $theDateTo->format('m');
            $firstDayOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01';
            $dailyTrends = DB::table('complaints as c')
               ->select(DB::raw('dayofmonth(c.created_at) as dayOfComplaint,
                                    COUNT(c.id) AS noOfComplaints'))
               ->where('c.client_id', '=', $dto->client_id)
               ->whereDate('c.created_at', '>=', $firstDayOfCurrentMonth)
               ->whereDate('c.created_at', '<=', $theDateTo)
               ->groupBy('dayOfComplaint')
               ->orderBy('dayOfComplaint')
               ->get();
         //
         $response=[
               'byDistrict' => $byDistrict,
               'byType' => $byType,
               'dailyTrends' => $dailyTrends->toArray(),
            ];

         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
