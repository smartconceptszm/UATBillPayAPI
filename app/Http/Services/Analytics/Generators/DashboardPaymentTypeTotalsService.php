<?php

namespace App\Http\Services\Analytics\Generators;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardPaymentTypeTotals;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardPaymentTypeTotalsService
{

   public function __construct(
         private DashboardPaymentTypeTotals $model
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
         $menuTotals = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->join('client_menus as cm','p.menu_id','=','cm.id')
                           ->select(DB::raw('cm.prompt AS paymentType,
                                                COUNT(p.id) AS numberOfTransactions,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->where('p.created_at', '>=' ,$params['dateFrom'])
                           ->where('p.created_at', '<=', $params['dateTo'])
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('cw.client_id', '=', $params['client_id'])
                           ->groupBy('cm.prompt')
                           ->get();
         $menuTotalRecords =[];
         foreach ($menuTotals as  $menuTotal) {
            $menuTotalRecords[] = ['client_id' => $params['client_id'],'paymentType' => $menuTotal->paymentType, 
                                    'year' => $params['theYear'], 'numberOfTransactions' => $menuTotal->numberOfTransactions
                                    ,'month' => $params['theMonth'],'dateOfTransaction' => $theDate->format('Y-m-d'),
                                    'day' => $params['theDay'],'totalAmount'=>$menuTotal->totalAmount];
         }

         $currentEntries = DashboardPaymentTypeTotals::where([
                     ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                     ['client_id', '=', $params['client_id']],
                  ])
                  ->pluck('id')
                  ->toArray();

         DashboardPaymentTypeTotals::destroy($currentEntries);

         DashboardPaymentTypeTotals::upsert(
                  $menuTotalRecords,
                  ['client_id','paymentType', 'dateOfTransaction'],
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
