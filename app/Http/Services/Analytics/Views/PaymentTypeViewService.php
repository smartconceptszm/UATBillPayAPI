<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentTypeViewService
{

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
