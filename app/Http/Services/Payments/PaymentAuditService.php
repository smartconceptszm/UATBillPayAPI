<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentAudit;
use Exception;

class PaymentAuditService
{

   public function __construct(
      private PaymentAudit $model
   ) {}

   public function updateHistory(string $id) : object|null {
      
      try {

         $records = DB::table('payments as p')
                        ->join('payment_audits as pa','pa.payment_id','=','p.id')
                        ->join('users as u','pa.user_id','=','u.id')
                        ->select('pa.oldValues','pa.newValues','pa.updateChannel','p.id','p.customerAccount',
                                       'p.mobileNumber','p.transactionId','p.ppTransactionId',
                                       'p.paymentStatus','p.receipt','p.receiptNumber')
                        ->where('p.id', '=', $id);

         return $records;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }


   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
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


