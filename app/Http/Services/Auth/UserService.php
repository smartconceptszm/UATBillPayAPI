<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Auth\UserGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
         if($criteria){
            return $this->model->where($criteria)->get()->all();
         }else{
            return $this->model->get()->all();
         }
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

   public function create(array $data):object|null
   {

      try {
         $group_id = '';
         $data['password'] = app('hash')->make($data['password']);
         foreach ( $data as $key => $value) {
            if($value == ''){
               unset($data[$key]);
            }
         }
         if(isset($data['group_id'])){
            $group_id = $data['group_id'];
            unset($data['group_id']);
         }

         DB::beginTransaction();
         try {
            $user = $this->model->create($data);
            if($group_id){
               $this->userGroupService->create(
                     [
                        'group_id' => $group_id,
                        'user_id' => $user->id
                     ]
                  );
            }
            DB::commit();
         } catch (Exception $e) {
               DB::rollBack();
               throw new Exception($e->getMessage());
         }
        return $user;
      } catch (\Exception $e) {
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

}
