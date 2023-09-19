<?php

namespace App\Http\Services\SMS;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class SMSDashboardService
{

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         //Get all in Date Range
            $theSMSs = DB::table('messages as sms')
               ->join('mnos as m','sms.mno_id','=','m.id')
               ->select('sms.*','m.name as mno','m.colour')
               ->where('sms.status', '=', 'DELIVERED')
               ->where('sms.client_id', '=', $dto->client_id)
               ->whereDate('sms.created_at', '>=', $dto->dateFrom)
               ->whereDate('sms.created_at', '<=', $dto->dateTo)
               ->get();
            $groupedData = $theSMSs->groupBy('type');
            $byType=[];
            foreach ($groupedData as $key => $value) {
               $byType[] = [
                     "type"=>$key,
                     "totalMessages" => $value->count('id')
                  ];
            }

            $groupedData = $theSMSs->groupBy('mno');
            $byMNO=[];
            foreach ($groupedData as $key => $value) {
               $firstRow = $value->first();
               $byMNO[]= [
                     "mno"=>$key,
                     "colour"=>$firstRow->colour,
                     "totalMessages"=>$value->count('id')
                  ];
            }
         // Get all Days in Current Month
            $theDateTo = Carbon::parse($dto->dateTo);
            $currentYear = $theDateTo->format('Y');
            $currentMonth = $theDateTo->format('m');
            $firstDayOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01';
            $dailyTrends = DB::table('messages as sms')
               ->select(DB::raw('dayofmonth(sms.created_at) as dayOfSMS,
                                    COUNT(sms.id) AS noOfSMSs'))
               ->where('sms.status', '=', 'DELIVERED')
               ->where('sms.client_id', '=', $dto->client_id)
               ->whereDate('sms.created_at', '>=', $firstDayOfCurrentMonth)
               ->whereDate('sms.created_at', '<=', $theDateTo)
               ->groupBy('dayOfSMS')
               ->orderBy('dayOfSMS')
               ->get();
         //
         $response=[
               'byMNO' => $byMNO,
               'byType' => $byType,
               'dailyTrends' => $dailyTrends->toArray(),
            ];

         return $response;
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
