<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\Schema;
use App\Models\SMSChannelCredentials;
use Exception;

class SMSChannelCredentialsService
{

   public function __construct(
         private SMSChannelCredentials $model
   ) {}

   public function findAll(?array $criteria):array|null
   {

      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function getSMSChannelCredentials(string $channel_id):array|null
   {

      try {
         $smsChannelCredentials = [];
         $records = $this->model->where(['channel_id'=>$channel_id])->get()->all();
         if($records){
            foreach ($records as $record) {
               $smsChannelCredentials[$record->key]=$record->keyValue;
            }
         }
         return $smsChannelCredentials;
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
