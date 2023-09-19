<?php

namespace App\Http\Services\MenuConfigs;

use App\Http\Services\CRM\ComplaintService;
use App\Models\ComplaintSubType;
use Exception;

class ComplaintSubTypeService
{

   public function __construct(
         private ComplaintSubType $model,
         private ComplaintService $complaintService
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
         foreach ( $data as $key => $value) {
            if($value == ''){
                  unset($data[$key]);
            }
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
         $registereComplaints = $this->complaintService->findOneBy(['complaint_subtype_id'=>$id]);
         if($registereComplaints){
            throw new Exception("Complaints sub types cannot be deleted because it has complaints registered", 1);
         }else{
            return $this->model->where('id', $id)->delete();
         }
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }


}
