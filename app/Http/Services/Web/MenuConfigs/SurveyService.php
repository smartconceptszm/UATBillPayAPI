<?php

namespace App\Http\Services\Web\MenuConfigs;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Models\Survey;
use Exception;

class SurveyService
{

   public function __construct(
         private Survey $model
   ) {}


   public function findAll(array $criteria = null):array|null
   {
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
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
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         foreach ( $data as $key => $value) {
            if($value == ''){
                  unset($data[$key]);
            }
         }
         if(Arr::exists($data, 'isActive')){
            $data['isActive'] = 'NO';
         }
         return $this->model->create($data);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function update(array $data, string $id) : object|null {

      try {
         if(\key_exists('isActive',$data)){
            if( $data['isActive'] == 'YES'){
               $activeSurvey = $this->findOneBy(['isActive' => 'YES']);
               if($activeSurvey){
                  $activeRecord = $this->model->findOrFail($activeSurvey->id);
                  $activeRecord->isActive = 'NO';
                  $activeRecord->save();
               }
            }
         }
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


