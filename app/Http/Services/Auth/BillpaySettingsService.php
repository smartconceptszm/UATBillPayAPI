<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\BillpaySettings;
use Illuminate\Support\Carbon;
use Exception;

class BillpaySettingsService
{

   public function __construct(
         private BillpaySettings $model
   ) {}

   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function getAllSettings():array|null
   {
      try {
         $billpaySettings = [];
         $records = $this->model->get()->all();
         if($records){
            foreach ($records as $record) {
               $billpaySettings[$record->key]=$record->keyValue;
            }
         }
         return $billpaySettings;
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
         $this->refreshCache();
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
         $this->refreshCache();
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id) : bool{
      try {
         $deleted = $this->model->where('id', $id)->delete();
         $this->refreshCache();
         return $deleted ;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   protected function refreshCache()
   {
      $billpaySettings = $this->getAllSettings();
      Cache::put('billpaySettings',\json_encode($billpaySettings),Carbon::now()->addHours(24));
   }

}
