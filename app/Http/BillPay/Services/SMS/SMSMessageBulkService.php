<?php

namespace App\Http\BillPay\Services\SMS;

use App\Http\BillPay\Services\Contracts\IFindByIdService;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Http\BillPay\Repositories\SMS\BulkSMSOfClientRepo;
use App\Http\BillPay\Repositories\SMS\BulkMessageRepo;
use Illuminate\Support\Facades\Auth;
use App\Http\BillPay\DTOs\SMSTxDTO;
use App\Jobs\BulkSMSJob;
use Exception;

class SMSMessageBulkService implements ICreateService, IFindByIdService, IFindAllService
{

   private $bulkSMSOfClientRepo;
   private $repository;
   public function __construct(BulkSMSOfClientRepo $bulkSMSOfClientRepo,
                                 BulkMessageRepo $repository)
   {
      $this->bulkSMSOfClientRepo = $bulkSMSOfClientRepo;
      $this->repository = $repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {
      try {
         return $this->bulkSMSOfClientRepo->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findById(string $id, array $fields = ['*']):object|null
   {

      try {
         return  $this->repository->findById($id,$fields);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
         
      }

   }

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $bulkSMS = $this->repository->create([
                        'description' => $data['description'],
                        'client_id' => $data['client_id'],
                        'user_id' => $user->id,
                        'type' => 'BULK',
                     ]);
         $chunkedArr=\array_chunk($data['mobileNumbers'],15,false);
         foreach ($chunkedArr as $mobileNumbersArr) {
            $arrSMSes=[];
            foreach ($mobileNumbersArr as $key => $value) {
               $dto = new SMSTxDTO();
               $arrSMSes[$key]= $dto->fromArray([
                                 'client_id'=>$data['client_id'],
                                 'mobileNumber'=>'26'.$value,
                                 'message'=>$data['message'],
                                 'bulk_id'=>$bulkSMS->id,
                                 'type'=>$bulkSMS->type
                           ]);
            }
            Queue::later(Carbon::now()->addSeconds(1), new BulkSMSJob($arrSMSes));
         }
         $this->responseDTO->description = "Messages successfully submitted";
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $this->responseDTO;
      
   }

}
