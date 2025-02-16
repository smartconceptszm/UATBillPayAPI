<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardPaymentTypeTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentTypeAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
         $menuTotals = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->join('client_menus as cm','p.menu_id','=','cm.id')
                           ->select(DB::raw('cm.prompt AS paymentType,
                                                COUNT(p.id) AS numberOfTransactions,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->where('p.created_at', '>=' ,$params['dateFrom'])
                           ->where('p.created_at', '<=', $params['dateTo'])
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('cw.client_id', '=', $params['client_id'])
                           ->groupBy('cm.prompt')
                           ->get();
         $menuTotalRecords =[];
         foreach ($menuTotals as  $menuTotal) {
            $menuTotalRecords[] = ['client_id' => $params['client_id'],'paymentType' => $menuTotal->paymentType, 
                                    'year' => $params['theYear'], 'numberOfTransactions' => $menuTotal->numberOfTransactions
                                    ,'month' => $params['theMonth'],'dateOfTransaction' => $theDate->format('Y-m-d'),
                                    'day' => $params['theDay'],'totalAmount'=>$menuTotal->totalAmount];
         }

         DashboardPaymentTypeTotals::upsert(
                  $menuTotalRecords,
                  ['client_id','paymentType', 'dateOfTransaction'],
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

         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         $thePayments = DB::table('dashboard_payment_type_totals as ptt')
                           ->select(DB::raw('ptt.paymentType,
                                                SUM(ptt.numberOfTransactions) AS totalTransactions,
                                                SUM(ptt.totalAmount) as totalRevenue'))
                           ->whereBetween('ptt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ptt.client_id', '=', $dto->client_id)
                           ->groupBy('paymentType');
         $menuTotals = $thePayments->get();
         $paymentTypeLabels = $menuTotals->map(function ($item) {
                                             return $item->paymentType."(".$item->totalTransactions.")";
                                          });
         $paymentTypeData = $menuTotals->map(function ($item) {
                                             return $item->totalRevenue;
                                          });
         $response = [
                        'paymentTypeLabels' =>$paymentTypeLabels,
                        'paymentTypeData' =>$paymentTypeData,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
