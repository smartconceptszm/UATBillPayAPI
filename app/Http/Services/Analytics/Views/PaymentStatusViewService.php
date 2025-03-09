<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentStatusViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $thePayments = DB::table('dashboard_payment_status_totals as pst')
                           ->select(DB::raw('pst.paymentStatus,
                                       SUM(pst.numberOfTransactions) AS totalTransactions,
                                       SUM(pst.totalAmount) as totalRevenue'))
                           ->whereBetween('pst.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('pst.client_id', '=', $dto->client_id)
                           ->groupBy('paymentStatus')
                           ->orderBy('paymentStatus');
         $thePayments = $thePayments->get();

         $theLabels = $thePayments->map(function ($item) {
                                 return $item->paymentStatus.' ('.number_format($item->totalTransactions,0,'.',',').')';
                              });

         $i=0;
         $theColours = $thePayments->map(function ($item) use(&$i)  {
                                 $i++;
                                 $colours = ChartColours::getColours($i);
                                 return $colours['borderColor'];
                              });

         $theData = $thePayments->pluck('totalRevenue')->values();;

         $datasets = [
                        collect([
                           'backgroundColor'=> $theColours ,
                           'data'=> $theData ,
                        ])
                     ];

         $response = [
                        'labels' => $theLabels,
                        'datasets' => $datasets
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
