<?php

namespace App\Http\Services\Clients;


use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\DB;
use Exception;

class MainDashboardService 
{

	public function __construct(
		private ClientService $clients)
	{}

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $response=[];
         $activeClients = $this->clients->findAll(['status' => 'ACTIVE']);
         if($activeClients){
            foreach ($activeClients as $activeClient) {
               //Get all in Date Range
                  $thePayments = DB::table('payments as p')
                     ->join('mnos as m','p.mno_id','=','m.id')
                     ->select('p.id','p.district','p.receiptAmount',
                                       'm.name as mno','m.colour')
                     ->whereIn('p.paymentStatus', 
                        ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                     ->where('p.client_id', '=', $activeClient->id)
                     ->whereDate('p.created_at', '>=', $dto->dateFrom)
                     ->whereDate('p.created_at', '<=', $dto->dateTo)
                     ->get();
                  $groupedData = $thePayments->groupBy('mno');
                  $byMNO=[];
                  foreach ($groupedData as $key => $value) {
                     $firstRow = $value->first();
                     $byMNO[]= [
                        "mno"=>$key,
                        "colour"=>$firstRow->colour,
                        "totalRevenue"=>$value->sum('receiptAmount')
                        ];
                  }
               //
               $response[] = [$activeClient,$byMNO];
            }
         }
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
