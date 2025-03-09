<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentTypeViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $thePayments = DB::table('dashboard_payment_type_totals as ptt')
                           ->select(DB::raw('ptt.paymentType,
                                                SUM(ptt.numberOfTransactions) AS totalTransactions,
                                                SUM(ptt.totalAmount) as totalRevenue'))
                           ->whereBetween('ptt.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('ptt.client_id', '=', $dto->client_id)
                           ->groupBy('paymentType');
         $thePayments = $thePayments->get();

         $theLabels = $thePayments->pluck('paymentType')->unique()->values();
         $i=0;
         $theData = $thePayments->map(function ($item) use(&$i)  {
                        $i++;
                        $colours = ChartColours::getColours($i);
                        return [
                           'label'=>$item->paymentType.
                                       ' ('.number_format($item->totalTransactions,0,'.',',').')',
                           'data'=>$item->totalRevenue,
                           'backgroundColor'=> $colours['backgroundColor'],
                           'borderColor' => $colours['borderColor'],
                           'pointBackgroundColor' => $colours['pointBackgroundColor'],
                           'pointBorderColor' => $colours['pointBorderColor'],
                           'fill' => false
                        ];
                     });

         $response = [
                        'labels' =>$theLabels,
                        'datasets' =>$theData,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
