<?php

namespace App\Http\Services\SMS;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Models\Message;
use Exception;


class MessageService
{

   public function __construct(
         private Message $model
   ) {}

   public function send(array $data):string
   {

      try {
			$user = Auth::user(); 
         $data['client_id'] = $user->client_id;
			$data['user_id'] = $user->id;
			Queue::later(Carbon::now()->addSeconds(1),new SendSMSesJob([$data],$user->urlPrefix));
         return 'Message submitted';
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function findAll(array $criteria = null):array|null
   {
      try {
         if($criteria){
            return $this->model->where($criteria)->get()->all();
         }else{
            return $this->model->orderBy('order')->get()->all();
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findById(string $id) : object|null {
      try {
         return $this->model->findOrFail($id);
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         return $this->model->where($criteria)->first();
      } catch (Exception $e) {
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
      } catch (Exception $e) {
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
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id) : bool{
      try {
         return $this->model->where('id', $id)->delete();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
