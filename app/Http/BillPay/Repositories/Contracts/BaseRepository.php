<?php

namespace App\Http\BillPay\Repositories\Contracts;

use App\Http\BillPay\Repositories\Contracts\IFindOneByRepository;
use App\Http\BillPay\Repositories\Contracts\IFindByIdRepository;
use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use App\Http\BillPay\Repositories\Contracts\ICreateRepository;
use App\Http\BillPay\Repositories\Contracts\IUpdateRepository;
use App\Http\BillPay\Repositories\Contracts\IDeleteRepository;
use Illuminate\Database\Eloquent\Model;
use Exception;

abstract class BaseRepository implements ICreateRepository, 
            IFindAllRepository, IFindByIdRepository,IFindOneByRepository, 
                                          IUpdateRepository,IDeleteRepository
{

   protected $model;
   public function __construct(Model $model)
   {
      $this->model = $model;
   }
   
   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $records = $this->model->select($fields)->where($criteria)->get();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records->all();

   }

   public function findById(string $id, array $fields = ['*']):object|null
   {

      try {
         return $this->model->select($fields)->findOrFail($id);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function findOneBy(array $criteria, array $fields = ['*']):object|null
   {

      try {
         return $this->model->select($fields)->where($criteria)->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function create(array $data):object|null
   {

      try {
         foreach ($data as $key => $value) {
            if($value == ''){
                  unset($data[$key]);
            }
         }
        return $this->model->create($data);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function update(array $data, string $id):object|null
   {

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
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id):bool
   {

      try {
         return $this->model->where('id', $id)->delete();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }
      
}