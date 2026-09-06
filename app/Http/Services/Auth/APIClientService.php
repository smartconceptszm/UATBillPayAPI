<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Models\APIClient;
use Exception;

class APIClientService
{

   public function __construct(
      private ClientService $clientService,
      private APIClient $model
   ) {}

   public function findAll(?array $criteria):array|null
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
            if (Schema::hasColumn($this->model->getTable(), $key) && $value != '') {
               $this->model->$key = $value;
            }
         }
         $this->model->save();

         //Send notification 
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $client = $this->clientService->findOneBy(['urlPrefix'=>$billpaySettings['DEFAULT_URL_PREFIX']]);

            $smses =[[
                     'mobileNumber' => $this->model->mobileNumber,
                     'client_id' => $client->id,
                     'urlPrefix'=>$client->urlPrefix,
                     'message' => "API Registratioon submitted. Please wait for approval",
                     'type' => "NOTIFICATION",
                  ]];

            SendSMSesJob::dispatch($smses)
                           ->delay(Carbon::now()->addSeconds(1))
                           ->onQueue('high');

         //

         return $this->model;
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
            if(isset($data['username']) && $data['username'] ==  $user->username ){
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
