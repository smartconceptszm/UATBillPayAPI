<?php

namespace App\Http\Services\Analytics\Generators;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardConsumerTypeTotals;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardConsumerTypeTotalsService
{

   public function __construct(
         private DashboardConsumerTypeTotals $model
   ) {}

   public function findAll(array $criteria = null):array|null
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
         $consumerTypeTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('p.consumerType,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->where('p.created_at', '>=' ,$params['dateFrom'])
                                 ->where('p.created_at', '<=', $params['dateTo'])
                                 ->whereIn('p.paymentStatus', 
                                          [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                             PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                                 ->where('cw.client_id', '=', $params['client_id'])
                                 ->groupBy('p.consumerType')
                                 ->get();

         $consumerTypeTotalRecords =[];
         foreach ($consumerTypeTotals as $consumerTypeTotal) {
            $consumerType = $consumerTypeTotal->consumerType? $consumerTypeTotal->consumerType:"OTHER";
            $consumerTypeTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                          'numberOfTransactions' => $consumerTypeTotal->numberOfTransactions,
                                          'totalAmount'=>$consumerTypeTotal->totalAmount, 'year' => $params['theYear'], 
                                          'dateOfTransaction' => $theDate->format('Y-m-d'),'consumerType' => $consumerType];
         }

         DashboardConsumerTypeTotals::upsert(
                  $consumerTypeTotalRecords,
                  ['client_id','consumerType', 'dateOfTransaction'],
                  ['numberOfTransactions','totalAmount','year','month','day']
               );

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
