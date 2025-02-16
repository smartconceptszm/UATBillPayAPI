<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardPaymentStatusTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentStatusAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];     
         $paymentStatusTotals = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->select(DB::raw('p.paymentStatus,
                                                COUNT(p.id) AS numberOfTransactions,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->where('p.created_at', '>=' ,$params['dateFrom'])
                           ->where('p.created_at', '<=', $params['dateTo'])
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('cw.client_id', '=', $params['client_id'])
                           ->groupBy('p.paymentStatus')
                           ->get();
         $paymentStatusRecords =[];
         foreach ($paymentStatusTotals as $paymentStatusTotal) {
            $paymentStatusRecords[] = ['client_id' => $params['client_id'], 'year' => $params['theYear'], 'month' => $params['theMonth'],
                                       'numberOfTransactions' => $paymentStatusTotal->numberOfTransactions,
                                       'paymentStatus' => $paymentStatusTotal->paymentStatus,'day' => $params['theDay'], 
                                       'dateOfTransaction' => $theDate->format('Y-m-d'),
                                       'totalAmount'=>$paymentStatusTotal->totalAmount];
         }

         $currentEntries = DashboardPaymentStatusTotals::where([
                                                ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                                                ['client_id', '=', $params['client_id']],
                                             ])
                                             ->pluck('id')
                                             ->toArray();

         DashboardPaymentStatusTotals::destroy($currentEntries);
         
         DashboardPaymentStatusTotals::upsert(
                  $paymentStatusRecords,
                  ['client_id','paymentStatus', 'dateOfTransaction'],
                  ['numberOfTransactions','totalAmount','year','month','day']
               );
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

         $thePayments = DB::table('dashboard_payment_status_totals as pst')
                           ->select(DB::raw('pst.paymentStatus,
                                       SUM(pst.numberOfTransactions) AS totalTransactions,
                                       SUM(pst.totalAmount) as totalRevenue'))
                           ->whereBetween('pst.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('pst.client_id', '=', $dto->client_id)
                           ->groupBy('paymentStatus')
                           ->orderBy('paymentStatus');
         // $theSQLQuery = $thePayments->toSql();
         // $theBindings = $thePayments-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $thePayments = $thePayments->get();

         $paymentStatusData = $thePayments->map(function ($item) {
                                                   return $item->totalRevenue;
                                             });
         $paymentStatusLabels = $thePayments->map(function ($item) {
                                                return $item->paymentStatus."(".$item->totalTransactions.")";
                                          });
         $paymentStatusColours = $thePayments->map(function ($item) use($billpaySettings) {
                                             return $billpaySettings[$item->paymentStatus.'_COLOUR'];
                                       });        
         //
         $response = [
                        'paymentStatusData' => $paymentStatusData,
                        'paymentStatusLabels' => $paymentStatusLabels,
                        'paymentStatusColours' => $paymentStatusColours
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
