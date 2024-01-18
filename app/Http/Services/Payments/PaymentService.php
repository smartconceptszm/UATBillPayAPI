<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use Exception;

class PaymentService
{

   public function __construct(
         private Payment $model
   ) {}

   public function findAll(array $criteria = null):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findById(string $id) : object|null {
      try {
         return $this->model->findOrFail($id);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         return $this->model->where($criteria)->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function create(array $data) : object|null {
      try {
         foreach ( $data as $key => $value) {
            if($value == ''){
                  unset($data[$key]);
            }
         }
        return $this->model->create($data);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function update(array $data, string $id) : object|null {

      try {
         foreach ($data as $key => $value) {
            if($key == 'id'){
               unset($data['id']);
            }
         }
         $record = $this->model->findOrFail($id);
         Log::info('(PUT Parameters): Attributes'.\json_encode($data));
         foreach ($data as $key => $value) {
            if(\strpos($key,'\/')){
               unset($data[$key]);
            }
         }
         Log::info('(UPDATE Parameters): Attributes'.\json_encode($data));
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


