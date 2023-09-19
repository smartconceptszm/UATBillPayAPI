<?php

namespace App\Http\Services\MenuConfigs;

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
         return $this->model->findOrFail($id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         return $this->model->where($criteria)->first();
      } catch (\Exception $e) {
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
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function update(array $data, string $id) : object|null {

      try {
         foreach ($data as $key => $value) {
            if($value == '' && $key != 'error'){
                  unset($data[$key]);
            } 
            if($key == 'id'){
               unset($data['id']);
            }
            if($key == 'isActive' && $value == 'YES'){
               $activeSurvey = $this->findOneBy(['isActive' => 'YES']);
               if($activeSurvey){
                  $activeRecord = $this->model->findOrFail($activeSurvey->id);
                  $activeRecord->isActive = 'NO';
                  $activeRecord->save();
               }
            }
         }
         $record = $this->model->findOrFail($id);
         foreach ($data as $key => $value) {
            $record->$key = $value;
         }
         if($record->isDirty()){
            $record->save();
         }
         return $record;
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id) : bool{
      try {
         return $this->model->where('id', $id)->delete();
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}


