<?php

namespace App\Http\Services\SMS\Generators;

use App\Models\SMSDashboardChannelTotals;
use App\Models\SMSDashboardTypeTotals;
use App\Models\Message;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SMSAnalyticsGeneratorService
{

   public function generate(array $params)
   {
      
      try {

         $theDate = $params['theDate'];
         
         //Step 1 Generate SMS Channel Daily Messages totals
            $smsProviderTotals = Message::select('channel', DB::raw('count(*) as numberOfMessages'),
                                                   DB::raw('SUM(amount) as totalAmount'))
                                    ->where('created_at', '>=' ,$params['dateFrom'])
                                    ->where('created_at', '<=', $params['dateTo'])
                                    ->where('client_id',$params['client_id'])
                                    ->where('status','DELIVERED')
                                    ->groupBy('channel')
                                    ->get();
                                    
            if($smsProviderTotals->isNotEmpty()){
               $smsProviderTotalRecords =[];
               foreach ($smsProviderTotals as $smsProviderTotal) {
                  $smsProviderTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                      'dateOfMessage' => $theDate->format('Y-m-d'),'year' => $params['theYear'],
                                                      'channel' => $smsProviderTotal->channel == ''? "UNKOWN":$smsProviderTotal->channel, 
                                                      'numberOfMessages' => $smsProviderTotal->numberOfMessages,
                                                      'totalAmount'=>$smsProviderTotal->totalAmount,];
               }
   
               SMSDashboardChannelTotals::upsert(
                        $smsProviderTotalRecords,
                        ['client_id','channel', 'dateOfMessage'],
                        ['numberOfMessages','totalAmount','year','month','day']
                     );
            }
         //

         //Step 2 - Generate SMS Type Daily transactions totals
            $smsTypeTotals = Message::select('type', DB::raw('count(*) as numberOfMessages'),
                                                   DB::raw('SUM(amount) as totalAmount'))
                                    ->where('created_at', '>=' ,$params['dateFrom'])
                                    ->where('created_at', '<=', $params['dateTo'])
                                    ->where('client_id',$params['client_id'])
                                    ->where('status','DELIVERED')
                                    ->groupBy('type')
                                    ->get();
            if($smsTypeTotals->isNotEmpty()){
               $smsProviderTotalRecords =[];
               foreach ($smsTypeTotals as $smsTypeTotal) {
                  $smsTypeTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                      'dateOfMessage' => $theDate->format('Y-m-d'),'year' => $params['theYear'],
                                                      'messageType' => $smsTypeTotal->type, 
                                                      'numberOfMessages' => $smsTypeTotal->numberOfMessages,
                                                      'totalAmount'=>$smsTypeTotal->totalAmount,];
               }
   
               SMSDashboardTypeTotals::upsert(
                        $smsTypeTotalRecords,
                        ['client_id','messageType', 'dateOfMessage'],
                        ['numberOfMessages','totalAmount','year','month','day']
                     );
            }
         //

      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

}
