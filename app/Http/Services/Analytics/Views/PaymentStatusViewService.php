<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentStatusViewService
{

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
