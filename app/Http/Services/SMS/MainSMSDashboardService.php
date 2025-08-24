<?php

namespace App\Http\Services\SMS;

use \App\Http\Services\Enums\ChartColours;
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
         $theYear = (string)$theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         //Get all in Date Range
         $theSMSes = DB::table('sms_dashboard_type_totals as sms')
                                 ->join('clients as c','sms.client_id','=','c.id')
                                 ->select(DB::raw('c.id,c.urlPrefix,c.name,sms.messageType,
                                                      SUM(sms.numberOfMessages) AS totalMessages'))
                                 ->where('sms.year', '=',  $theYear)
                                 ->where('sms.month', '=',  $theMonth)
                                 ->groupBy('c.id','c.urlPrefix','c.name')
                                 ->groupBy('sms.messageType')
                                 ->orderBy('totalMessages','desc');

                                 

         $theSQLQuery = $theSMSes->toSql();
         $theBindings = $theSMSes-> getBindings();
         $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);

         $theSMSes = $theSMSes->get();

         if($theSMSes->isNotEmpty()){
            $billpaySettings = \json_decode(\cache('billpaySettings',\json_encode([])), true);
            $smsesByClient = $theSMSes->groupBy('urlPrefix');

            $smsesSummary = $smsesByClient->map(function ($client) use ($billpaySettings) {
                                 $clientDetails = $client->get(0);
                                 $colourRef = 4;
                                 $formattedData = $client->map(function ($item) use (&$colourRef){
                                                $colourRef++;
                                                $colour = ChartColours::getColours($colourRef);
                                                $theFormattedData = [
                                                                        'messageType'=>$item->messageType,
                                                                        'totalMessages'=>number_format($item->totalMessages,0,'.',','),
                                                                        'colour'=>$colour['backgroundColor']
                                                                     ];

                                                return $theFormattedData;
                                             });
                                 $totalMessages = $client->reduce(function ($messages, $item) {
                                                return $messages + $item->totalMessages;
                                          }); 
                                 $formattedData->prepend([
                                          'messageType'=>'TOTAL',
                                          'totalMessages'=>number_format($totalMessages,0,'.',','),
                                          'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                                    ]);

                                 return [
                                          'urlPrefix'=>$clientDetails->urlPrefix,
                                          'name'=>$clientDetails->name,
                                          'id'=>$clientDetails->id,
                                          'totalMessages' =>  $totalMessages,
                                          'data'=>$formattedData->toArray()
                                       ];
                              });

            $smsesSummary = $smsesSummary->sortByDesc('totalMessages',SORT_NUMERIC);
            return $smsesSummary;
         }else{
               return [];
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
