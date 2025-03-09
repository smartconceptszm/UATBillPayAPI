<?php

namespace App\Http\Services\Analytics\Views;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentProviderSummaryUserViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;

         $thePayments = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                           ->select(DB::raw('pp.shortName as paymentsProvider,pp.colour,
                                                COUNT(p.id) AS numberOfTransactions,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->where('p.created_at', '>=' ,$dto->dateFromYMD)
                           ->where('p.created_at', '<=', $dto->dateToYMD)
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('p.revenueCollector', '=', $dto->revenueCollectorCode)
                           ->where('cw.client_id', '=', $dto->client_id)
                           ->groupBy('paymentsProvider','colour')
                           ->get();

         $theLabels = $thePayments->pluck('paymentsProvider')->unique()->values();
         $theLabels->prepend('TOTAL');
         $theData = $thePayments->map(function ($item) {
                                          return [
                                             'label'=>$item->paymentsProvider.
                                                         ' ('.number_format($item->numberOfTransactions,0,'.',',').')',
                                             'data'=>$item->totalAmount,
                                             'labelColour'=>$item->paymentsProvider,
                                             'colour'=>$item->colour
                                          ];
                                       });

         $theData->prepend([  
                        'label'=> 'TOTAL ('.number_format($thePayments->sum('numberOfTransactions'),0,'.',',') .')',
                        'data'=>$thePayments->sum('totalAmount'),
                        'labelColour'=>"TOTAL",
                        'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                     ]);
   
         return [
                  'labels' => $theLabels,
                  'datasets' => $theData
               ];

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
