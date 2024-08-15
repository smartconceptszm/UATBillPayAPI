<?php

namespace App\Http\Services\Web\Clients;

use App\Models\ClientMenu;
use Exception;

class ClientMenuService
{

   public function __construct(
         private ClientMenu $model
   ) {}

   public function findAll(array $criteria = null):array|null
   {
      try {
         return $this->model->where($criteria)->orderBy('order')->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findById(string $id) : object|null {
      try { 
         $clientMenu = $this->model->findOrFail($id);
         $clientMenu = \is_null($clientMenu)?null:(object)$clientMenu->toArray();
         return $clientMenu;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         $clientMenu = $this->model->where($criteria)->first();
         $clientMenu = \is_null($clientMenu)?null:(object)$clientMenu->toArray();
         return $clientMenu;
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
