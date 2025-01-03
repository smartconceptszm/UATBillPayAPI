<?php

namespace App\Http\Services\SMS;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MainSMSDashboardService
{

   public function findAll(array $criteria)
   {
      
      try {
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m-d',$dto->theMonth.'-01');
         $startOfMonth = $theDate->copy()->startOfMonth();
         $endOfMonth = $theDate->copy()->endOfMonth();
         //Get all in Date Range
            $theSMSes = DB::table('messages as sms')
               ->join('mnos as m','sms.mno_id','=','m.id')
               ->join('clients as c','sms.client_id','=','c.id')
               ->select(DB::raw('c.id,c.urlPrefix,c.name,m.name as mno,m.colour,
                                    COUNT(sms.id) AS totalSMSes'))
               ->where('sms.status', '=', 'DELIVERED')
               ->whereBetween('sms.created_at', [$startOfMonth,$endOfMonth])
               ->groupBy('c.id','c.urlPrefix','c.name')
               ->groupBy('mno','m.colour')
               ->orderBy('totalSMSes','desc')
               ->get();
            if($theSMSes->isNotEmpty()){
               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
               $smsByClient = $theSMSes->groupBy('urlPrefix');
               $smsesSummary = $smsByClient->map(function ($client) use ($billpaySettings) {
                  $clientDetails = $client->get(0);   
                  $formattedData = $client->map(function ($item) {
                     return [
                                    'smsProvider'=>$item->mno,
                                    'totalTransactions'=>$item->totalSMSes,
                                    'colour'=>$item->colour
                                 ];
                              });
                  $totalTransactions = $client->reduce(function ($transactions, $item) {
                                                      return $transactions + $item->totalSMSes;
                                                   }); 
                  $formattedData->prepend([
                        'smsProvider'=>'TOTAL',
                        'totalTransactions'=>$totalTransactions,
                        'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                     ]);
                  
                  return [
                           'urlPrefix'=>$clientDetails->urlPrefix,
                           'name'=>$clientDetails->name,
                           'id'=>$clientDetails->id,
                           'data'=>$formattedData->toArray()
                        ];
               });
               return $smsesSummary;
            }else{
               return [];
            }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
