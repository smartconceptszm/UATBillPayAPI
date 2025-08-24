<?php

namespace App\Http\Services\SMS;

use \App\Http\Services\Enums\ChartColours;
use App\Models\SMSDashboardChannelTotals;
use App\Models\SMSDashboardTypeTotals;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class ClientSMSDashboardService
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         //Get by Channel in Date Range
            $theMessages = SMSDashboardChannelTotals::select('channel')
                                                ->selectRaw('SUM(numberOfMessages) as totalMessages')
                                                ->whereBetween('dateOfMessage', [$dto->dateFrom, $dto->dateTo])
                                                ->where('client_id',$dto->client_id)
                                                ->groupBy('channel')
                                                ->get();

            $theData = $theMessages->pluck('totalMessages')->unique()->values();
            $theLabels = $theMessages->map(function ($item) {
                                    return $item->channel.' ('.number_format($item->totalMessages,0,'.',',').')';
                                 });
            $colours = ChartColours::getColours(1);
            $datasets = [collect([
                              'label'=>'Messages by Channel',
                              'data'=>$theData->toArray(),
                              'backgroundColor'=> $colours['backgroundColor'],
                              'borderColor' => $colours['borderColor'],
                              'pointBackgroundColor' => $colours['pointBackgroundColor'],
                              'pointBorderColor' => $colours['pointBorderColor'],
                              'fill' => false
                           ])];
   
            $byChannel = [
                           'labels' =>$theLabels,
                           'datasets' =>$datasets,
                        ];

         //
         //Get by Type in Date Range
            $typeMessages = SMSDashboardTypeTotals::select('messageType as type')
                                                ->selectRaw('SUM(numberOfMessages) as totalMessages')
                                                ->whereBetween('dateOfMessage', [$dto->dateFrom, $dto->dateTo])
                                                ->where('client_id',$dto->client_id)
                                                ->groupBy('messageType')
                                                ->get();
            $theData = $typeMessages->pluck('totalMessages')->unique()->values();
            $theLabels = $typeMessages->map(function ($item) {
                                    return $item->type.' ('.number_format($item->totalMessages,0,'.',',').')';
                                 });
            $colours = ChartColours::getColours(2);
            $datasets = [collect([
                              'label'=>'Messages Type',
                              'data'=>$theData->toArray(),
                              'backgroundColor'=> $colours['backgroundColor'],
                              'borderColor' => $colours['borderColor'],
                              'pointBackgroundColor' => $colours['pointBackgroundColor'],
                              'pointBorderColor' => $colours['pointBorderColor'],
                              'fill' => false
                           ])];
   
            $byType = [
                           'labels' =>$theLabels,
                           'datasets' =>$datasets,
                        ];
         //
         // Get all Days in Current Month
            $dateFrom = Carbon::parse($dto->dateFrom)->startOfDay();
            $dateTo = Carbon::parse($dto->dateTo)->endOfDay();
            $theMessages = SMSDashboardTypeTotals::select('day')
                                             ->selectRaw('SUM(numberOfMessages) as totalMessages')
                                             ->whereBetween('dateOfMessage', [$dto->dateFrom, $dto->dateTo])
                                             ->where('client_id',$dto->client_id)
                                             ->groupBy('day')
                                             ->get();

            $period = CarbonPeriod::create($dto->dateFrom, $dto->dateTo);
            if($dateFrom->month != $dateTo->month){
               $period = CarbonPeriod::create($dateFrom, $dateFrom->copy()->endOfMonth());
            }

            $dailyLabels =[];
            $dailyData = [];
            foreach ($period as $date) {
               $daysRecord = $theMessages->firstWhere('day','=',$date->day);
               $dailyLabels[] = $date->day;
               if($daysRecord){
                  $dailyData[] = $daysRecord->totalMessages;
               }else{
                  $dailyData[] = 0;
               }
            }

            $dailyTrends['labels'] = collect($dailyLabels);
            $colours = ChartColours::getColours(1);
            $dailyTrends['datasets'][] = collect([
                                             'backgroundColor'=> $colours['backgroundColor'],
                                             'borderColor' => $colours['borderColor'],
                                             'pointBackgroundColor' => $colours['pointBackgroundColor'],
                                             'pointBorderColor' => $colours['pointBorderColor'],
                                             'label' => $dateFrom->copy()->format('M-Y'),
                                             'data' => collect($dailyData),
                                             'fill' => false
                                          ]);


         //
         $response=[
               'byChannel' => $byChannel,
               'byType' => $byType,
               'dailyTrends' => $dailyTrends,
            ];

         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
