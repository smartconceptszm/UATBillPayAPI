<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Auth\UserGroupService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class UserService
{

   public function __construct(
      private UserGroupService $userGroupService,
      private User $model
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

   public function create(array $data):object|null
   {

      try {
         $data['password'] = app('hash')->make($data['password']);
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

   public function update(array $data, string $id):object|null
   {

      try {
         if(isset($data['password'])){
               $data['password'] = app('hash')->make($data['password']);
         }
         if(isset($data['status']) && $data['status'] != 'ACTIVE'){
            $user = Auth::user(); 
            if($data['username'] ==  $user->username ){
               throw new Exception("Logged in user cannot be de-activated!", 1);
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

}
