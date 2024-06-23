<?php

namespace App\Http\Services\Web\Clients;


use App\Http\Services\Web\Clients\ClientService;
use Illuminate\Support\Facades\DB;
use Exception;

class MainDashboardService 
{

	public function __construct(
		private ClientService $clients)
	{}

   public function findAll(array $criteria):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $response=[];
         $activeClients = $this->clients->findAll(['status' => 'ACTIVE']);
         if($activeClients){
            foreach ($activeClients as $activeClient) {
               //Get all in Date Range
                  $thePayments = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')
                                 ->select('p.id','p.receiptAmount',
                                                   'pp.shortName as paymentsProvider','pp.colour')
                                 ->whereBetween('p.created_at', [$dto->dateFrom, $dto->dateTo])
                                 ->where('p.client_id', '=', $activeClient->id)
                                 ->whereIn('p.paymentStatus', 
                                          ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                 ->get();
                  $groupedData = $thePayments->groupBy('paymentsProvider');
                  $byPaymentsProvider=[];
                  foreach ($groupedData as $key => $value) {
                     $firstRow = $value->first();
                     $byPaymentsProvider[]= [
                        "paymentsProvider"=>$key,
                        "colour"=>$firstRow->colour,
                        "totalRevenue"=>$value->sum('receiptAmount')
                        ];
                  }
               //
               $response[] = [$activeClient,$byPaymentsProvider];
            }
         }
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
