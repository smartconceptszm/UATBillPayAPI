<?php

namespace App\Http\Services\Analytics\Views;

use App\Http\Services\Enums\PaymentStatusEnum;
use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class RevenuePointUserViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $thePayments = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->select(DB::raw('p.revenuePoint,
                                             COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                           ->where('p.created_at', '>=' ,$dto->dateFromYMD)
                           ->where('p.created_at', '<=', $dto->dateToYMD)
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('p.revenueCollector', '=', $dto->revenueCollectorCode)
                           ->where('cw.client_id', '=', $dto->client_id)
                           ->groupBy('revenuePoint')
                           ->get();

         $theLabels = $thePayments->map(function ($item) {
                              return $item->revenuePoint.' ('.number_format($item->numberOfTransactions,0,'.',',').')';
                           });

         $theData = $thePayments->pluck('totalAmount')->unique()->values();

         $colours = ChartColours::getColours(3);
         $datasets = [collect([
                        'label'=>'Collections by Revenue Point',
                        'data'=>$theData->toArray(),
                        'backgroundColor'=> $colours['backgroundColor'],
                        'borderColor' => $colours['borderColor'],
                        'pointBackgroundColor' => $colours['pointBackgroundColor'],
                        'pointBorderColor' => $colours['pointBorderColor'],
                        'fill' => false
                     ])];

         $response = [
                        'labels' =>$theLabels,
                        'datasets' =>$datasets,
                     ];
   
         return $response;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
