<?php

namespace App\Http\Services\Analytics;

use App\Models\DashboardPaymentsProviderTotals;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class PaymentProviderAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
         $paymentsProviderTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('cw.payments_provider_id,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->where('p.created_at', '>=' ,$params['dateFrom'])
                              ->where('p.created_at', '<=', $params['dateTo'])
                              ->whereIn('p.paymentStatus', 
                                       [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                          PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->groupBy('cw.payments_provider_id')
                              ->get();
         if($paymentsProviderTotals->isNotEmpty()){
            $paymentsProviderTotalRecords =[];
            foreach ($paymentsProviderTotals as $paymentsProviderTotal) {
               $paymentsProviderTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                   'dateOfTransaction' => $theDate->format('Y-m-d'),'year' => $params['theYear'],
                                                   'payments_provider_id' => $paymentsProviderTotal->payments_provider_id, 
                                                   'numberOfTransactions' => $paymentsProviderTotal->numberOfTransactions,
                                                   'totalAmount'=>$paymentsProviderTotal->totalAmount,];
            }

            DashboardPaymentsProviderTotals::upsert(
                     $paymentsProviderTotalRecords,
                     ['client_id','payments_provider_id', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         }
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

   public function findAll(array $criteria):array|null
   {
      
      try {

         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                           ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                   SUM(ppt.totalAmount) as totalRevenue'))
                           ->whereBetween('ppt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ppt.client_id', '=', $dto->client_id)
                           ->groupBy('paymentsProvider','colour')
                           ->orderByDesc('totalRevenue');
         // $theSQLQuery = $thePayments->toSql();
         // $theBindings = $thePayments-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $byPaymentProvider = $thePayments->get();

         $totalRevenue = $byPaymentProvider->reduce(function ($totalRevenue, $item) {
                                             return $totalRevenue + $item->totalRevenue;
                                       });

         $totalRevenue = $totalRevenue?$totalRevenue:0;

         $paymentsSummary = $byPaymentProvider->map(function ($item) {
                                                   return [
                                                      'paymentsProvider'=>$item->paymentsProvider,
                                                      'totalAmount'=>$item->totalRevenue,
                                                      'colour'=>$item->colour
                                                   ];
                                             });
         $paymentsSummary->prepend([
                                    'paymentsProvider'=>'TOTAL',
                                    'totalAmount'=> $totalRevenue,
                                    'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                              ]);

         $response = [
                        'paymentsSummary' => $paymentsSummary,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function daily(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');


         //2. Daily Totals over the Month
         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                                 ->select(DB::raw('ppt.year,ppt.month,ppt.day,
                                                   SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                   SUM(ppt.totalAmount) as totalRevenue'))
                                 ->whereBetween('ppt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                                 ->where('ppt.client_id', '=', $dto->client_id)
                                 ->groupBy('day','month','year')
                                 ->orderBy('day');
         $dailyTrends = $thePayments->get();

         $period = CarbonPeriod::create($dateFrom, $dateTo);
         if($dateFrom->month != $dateTo->month){
            $period = CarbonPeriod::create($dateFrom, $dateFrom->copy()->endOfMonth());
         }

         $dailyLabels =[];
         $dailyData = [];
         foreach ($period as $date) {
            $daysRecord = $dailyTrends->firstWhere('day','=',$date->day);
            $dailyLabels[] = $date->day;
            if($daysRecord){
               $dailyData[] = $daysRecord->totalRevenue;
            }else{
               $dailyData[] = 0;
            }
         }

         $dailyTrends = [
                  [
                     'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                     'borderColor' => "rgba(75,192,192,1)",
                     'pointBackgroundColor' => "rgba(75,192,192,1)",
                     'pointBorderColor' => "rgba(75,192,192,1)",
                     'label' => "Daily revenue - ".$dateFrom->copy()->format('M-Y'),
                     'data' => $dailyData
                  ]
               ];

         $cumulativeTotal = 0;
         $cumulativeTrends = [];

         foreach ($dailyData as $amount) {
            $cumulativeTotal += $amount;
            $cumulativeTrends[] = $cumulativeTotal;
         }

         $dailyCumulativeTrends = [
                  [
                     'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                     'borderColor' => "rgba(75,192,192,1)",
                     'pointBackgroundColor' => "rgba(75,192,192,1)",
                     'pointBorderColor' => "rgba(75,192,192,1)",
                     'label' => "Cummulative Daily revenue - ".$dateFrom->copy()->format('M-Y'),
                     'data' => $cumulativeTrends
                  ]
               ];


         $dateFromPreviousMonth = $dateFrom->copy()->subMonth();
         $dateToPreviousMonth = $dateTo->copy()->subMonth();
         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->select(DB::raw('ppt.year,ppt.month,ppt.day,
                                             SUM(ppt.numberOfTransactions) AS totalTransactions,
                                             SUM(ppt.totalAmount) as totalRevenue'))
                           ->whereBetween('ppt.dateOfTransaction', 
                                             [$dateFromPreviousMonth->copy()->format('Y-m-d'), 
                                                $dateToPreviousMonth->copy()->format('Y-m-d')])
                           ->where('ppt.client_id', '=', $dto->client_id)
                           ->groupBy('day','month','year')
                           ->orderBy('day');
         $dailyTrendsLastMonth = $thePayments->get();

         $period = CarbonPeriod::create($dateFromPreviousMonth, $dateToPreviousMonth);
         if($dateFromPreviousMonth->month != $dateToPreviousMonth->month){
            $period = CarbonPeriod::create($dateFrom, $dateFromPreviousMonth->copy()->endOfMonth());
         }

         $dailyDataLastMonth = [];
         foreach ($period as $date) {
            $daysRecord = $dailyTrendsLastMonth->firstWhere('day','=',$date->day);
            if($daysRecord){
               $dailyDataLastMonth[] = $daysRecord->totalRevenue;
            }else{
               $dailyDataLastMonth[] = 0;
            }
         }

         $dailyTrends[] =  [
                     'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                     'borderColor' => "rgba(248,177,20,0.5)",
                     'pointBackgroundColor' => "rgba(248,177,20,0.5)",
                     'pointBorderColor' => "rgba(248,177,20,0.5)",
                     'label' => "Daily revenue - ".$dateFromPreviousMonth->copy()->format('M-Y'),
                     'data' => $dailyDataLastMonth
                  ];

         $cumulativeTotal = 0;
         $cumulativeLastMonthTrends = [];

         foreach ($dailyDataLastMonth as $amount) {
            $cumulativeTotal += $amount;
            $cumulativeLastMonthTrends[] = $cumulativeTotal;
         }

         $dailyCumulativeTrends[] =  [
                     'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                     'borderColor' => "rgba(248,177,20,0.5)",
                     'pointBackgroundColor' => "rgba(248,177,20,0.5)",
                     'pointBorderColor' => "rgba(248,177,20,0.5)",
                     'label' => "Daily revenue - ".$dateFromPreviousMonth->copy()->format('M-Y'),
                     'data' => $cumulativeLastMonthTrends
                  ];
         $response = [
                     'dailyTrendsData' => collect($dailyTrends),
                     'dailyTrendsLabels' => collect($dailyLabels),
                     'dailyCumulativeTrendsData' => collect($dailyCumulativeTrends),
                  ];
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }



}
