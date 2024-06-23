<?php

namespace App\Http\Services\Web\MenuConfigs;

use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintType;
use Illuminate\Support\Arr;
use Exception;

class ComplaintTypeService
{

   public function __construct(
         private ComplaintType $model
   ) {}

   public function findAll(array $criteria = null):array|null
   {
      try {
         if(!Arr::exists($criteria,"client_id")){
            $user = Auth::user(); 
            $criteria['client_id'] = $user->client_id;
         }
         return $this->model->where($criteria)->orderBy('order')->get()->all();
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
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
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

