<?php

namespace App\Http\Services\Analytics\Dashboards;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardHourlyTotals;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardHourlyTotalsService
{

   public function __construct(
         private DashboardHourlyTotals $model
   ) {}

   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function generate(array $params)
   {

      try {
         $theDate = $params['theDate'];
         $hourlyTotals = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->select(DB::raw('HOUR(p.created_at) AS hour,
                                             COUNT(p.id) AS numberOfTransactions,
                                                SUM(p.receiptAmount) as totalAmount'))
                        ->where('p.created_at', '>=' ,$params['dateFrom'])
                        ->where('p.created_at', '<=', $params['dateTo'])
                        ->whereIn('p.paymentStatus', 
                                 [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $params['client_id'])
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->get();
         if($hourlyTotals->isNotEmpty()){
            $hourlTotalRecords =[];
            foreach ($hourlyTotals as $hourlTotal) {
               $hourlTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                'numberOfTransactions' => $hourlTotal->numberOfTransactions,
                                                'totalAmount'=>$hourlTotal->totalAmount, 'year' => $params['theYear'], 
                                                'dateOfTransaction' => $theDate->format('Y-m-d'),
                                                'hour' => $hourlTotal->hour
                                             ];
            }

            DashboardHourlyTotals::upsert(
                  $hourlTotalRecords,
                  ['client_id','payments_provider_id', 'dateOfTransaction','hour'],
                  ['numberOfTransactions','totalAmount','year','month','day']
               );
         }
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

   public function findById(string $id) : object|null {
      try {
         $item = $this->model->findOrFail($id);
         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         $item = $this->model->where($criteria)->first();
         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function create(array $data) : object|null {
      try {
         foreach ( $data as $key => $value) {
            if (Schema::hasColumn($this->model->getTable(), $key) && $value != '') {
               $this->model->$key = $value;
            }
         }
         $this->model->save();
         return $this->model;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function update(array $data, string $id) : object|null {

      try {
         unset($data['id']);
         $record = $this->model->findOrFail($id);
         foreach ($data as $key => $value) {
            $record->$key = $value;
         }
         if($record->isDirty()){
            $record->save();
         }
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id) : bool{
      try {
         return $this->model->where('id', $id)->delete();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
