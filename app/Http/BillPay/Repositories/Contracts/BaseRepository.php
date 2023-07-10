<?php

namespace App\Http\BillPay\Repositories\Contracts;

use App\Http\BillPay\Repositories\Contracts\IFindOneByRepository;
use App\Http\BillPay\Repositories\Contracts\IFindByIdRepository;
use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use App\Http\BillPay\Repositories\Contracts\ICreateRepository;
use App\Http\BillPay\Repositories\Contracts\IUpdateRepository;
use App\Http\BillPay\Repositories\Contracts\IDeleteRepository;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseRepository implements ICreateRepository, 
            IFindAllRepository, IFindByIdRepository,IFindOneByRepository, 
                                          IUpdateRepository,IDeleteRepository
{
   
      public function findAll(array $criteria = null):array|null
      {

         try {
            $records=DB::table($this->table)->get();
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }
         return $records->all();

      }

      public function findById(string $id):object|null
      {

         try {
            $record = DB::table($this->table)->find((int)$id); 
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }
         return $record;

      }

      public function findOneBy(array $criteria):object|null
      {

         try {
            $query=DB::table($this->table);
            foreach ($criteria as $key => $value) {
               if($value != ''){
                     $query=$query->where($key, $value);
               }
            }
            $record = $query->first();
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }
         return $record;

      }
  

      public function create(array $data):object|null
      {

         try {
            foreach ($data as $key => $value) {
               if($value == ''){
                     unset($data[$key]);
               }
            }
            $id = DB::table($this->table)->insertGetId($data);
            if(!$id){
               throw new Exception("Record not created");
            }
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }
         return $this->findById($id);

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
            DB::table($this->table)
                        ->where('id', $id)
                        ->update($data);
            return $this->findById($id);
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }

      }
  
      public function delete(string $id):bool
      {

         try {
            $deleted = DB::table($this->table)->where('id', '=', $id)->delete();
            if(!$deleted ){
               return false;
            }
            return true;
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         }

      }
      
}