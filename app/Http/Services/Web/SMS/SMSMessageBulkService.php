<?php

namespace App\Http\Services\Web\SMS;

use App\Http\Services\Web\Clients\ClientService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\BulkMessage;
use App\Jobs\SendSMSesJob;
use Exception;

class SMSMessageBulkService
{

   public function __construct(
      private ClientService $clientService,
      private BulkMessage $model
   ) {}

   public function findAll(array $criteria):array|null
   {
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('bulk_messages as m')
                 ->select('*')
                 ->where('m.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
             $records =$records->whereBetween('m.created_at', [$dto->dateFrom, $dto->dateTo]);
         }
         $records = $records->get();
         return $records->all();
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

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['type'] = 'BULK';
         $data['user_id'] = $user->id;
         $bulkSMS = $this->model->create($data);
         $chunkedArr = \array_chunk($data['mobileNumbers'],15,false);
         
         $theClient = $this->clientService->findById($data['client_id']);

         foreach ($chunkedArr as $mobileNumbersArr) {
            $arrSMSes=[];
            foreach ($mobileNumbersArr as $key => $value) {
               $arrSMSes[$key]= [
                                 'mobileNumber'=>$value['mobileNumber'],
                                 'urlPrefix' => $theClient->urlPrefix,
                                 'client_id'=>$data['client_id'],
                                 'message'=>$data['message'],
                                 'bulk_id'=>$bulkSMS->id,
                                 'type'=>$bulkSMS->type
                           ];
            }
            Queue::later(Carbon::now()->addSeconds(1), new SendSMSesJob($arrSMSes,''),'','low');
         }
         return (object)["description" => "Messages successfully submitted"];
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
