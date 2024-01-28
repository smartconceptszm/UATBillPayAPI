<?php

namespace App\Http\Services\SMS;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\BulkMessage;
use App\Jobs\SendSMSesJob;
use Exception;

class SMSMessageBulkCustomService
{

   public function __construct(
         private BulkMessage $model
   ) {}

   public function findAll(array $criteria = null):array|null
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
      } catch (Exception$e) {
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

   public function create(array $data) : object|null {
      
      try {
         $user = Auth::user(); 
         $data['type'] = 'BULKCUSTOM';
         $data['user_id'] = $user->id;
         $bulkSMS = $this->model->create($data);

         $chunkedArr = \array_chunk($data['mobileNumbers'],15,false);
         
         foreach ($chunkedArr as $mobileNumbersArr) {
            $arrSMSes=[];
            foreach ($mobileNumbersArr as $key => $value) {
               $arrSMSes[$key]= [
                                 'client_id'=>$data['client_id'],
                                 'mobileNumber'=>$value['mobileNumber'],
                                 'message'=>$value['message'],
                                 'bulk_id'=>$bulkSMS->id,
                                 'type'=>$bulkSMS->type
                           ];
            }
            Queue::later(Carbon::now()->addSeconds(1), new SendSMSesJob($arrSMSes,''));
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
