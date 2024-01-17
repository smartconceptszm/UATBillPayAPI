<?php

namespace App\Http\Services\SMS;

use App\Http\Services\External\SMSClients\ISMSClient;
use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\SMS\MessageService;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Enums\MNOs;   
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;
use Exception;

class SMSService
{

   public function __construct(
      private MessageService $messageService, private ISMSClient $smsClient, 
      private ClientMnoService $ClientMnoService, private MnoService $mnoService, 
      private ClientService $clientService,private SMSTxDTO $smsTxDTO )
   {}

   public function send(BaseDTO $dto):BaseDTO|null
   {

      try {
         //Get client details
         $dto = $this->getClient($dto);
         //Get receipient MNO
         if(!$dto->mno_id){
            $mnoName = MNOs::getMNO(\substr($dto->mobileNumber,0,5));
            $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);
            $dto->mno_id = $mno->id;
         }
         //Take Care of Charges
         if($this->smsClient->channelChargeable()){
            $dto = $this->getCharges($dto);
         }
         //Send the SMS
         $dto = $this->sendMessage($dto);
         // Save the Record
         DB::beginTransaction();
         try {
            $sms = $this->messageService->create($dto->toSMSMessageData());
            if($sms->status == 'DELIVERED' && $this->smsClient->channelChargeable()){
               $this->clientService->update(['balance'=>$dto->balance],$dto->client_id);
            }
            DB::commit();
         } catch (\Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
         }
         $dto->status = $sms->status;
         $dto->id = $sms->id;
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $dto->error = $e->getMessage();
         }else{
            $dto->error = 'Error at  SMSService@create. '.$e->getMessage();
         }
         $dto->status="FAILED";
      }
      return $dto;

   }

   public function sendMany(array $arrSMSes): void
   {

      try {
         foreach ($arrSMSes as $smsData) {
            $this->send($this->smsTxDTO->fromArray($smsData));
         }
      } catch (\Throwable $e) {
         # code...
      }

   }

   private function getClient(BaseDTO $dto):BaseDTO|null
   {

      if($dto->client_id!=''){
         $client = $this->clientService->findById($dto->client_id);
         $dto->urlPrefix = $client->urlPrefix;
      }else{
         $client = $this->clientService->findOneBy($dto->urlPrefix);
         $dto->client_id = $client->id;
      }
      $dto->smsPayMode = $client->smsPayMode;
      $dto->shortName = $client->shortName;
      $dto->balance = $client->balance;
      return $dto;

   }

   private function getCharges(BaseDTO $dto):BaseDTO|null
   {

      //Get SMS Charge
         $ClientMnos = $this->ClientMnoService->findOneBy([
                                 'client_id'=>$dto->client_id,
                                 'mno_id'=> $dto->mno_id
                              ]);
         $dto->smsCharge = (float)$ClientMnos->smsCharge;
      //

      //Check if Client has enough balance
         if(($dto->smsPayMode == 'POST-PAID') || !($dto->balance < $dto->smsCharge)){
               $dto->balance = $dto->balance - $dto->smsCharge;
         }else{
               throw new Exception("Insufficient balance to send SMS", 1);
         }
      //
      return $dto;

   }

   private function sendMessage(BaseDTO $dto):BaseDTO|null
   {
      //Send the SMS
         if(!$dto->error){
            if($this->smsClient->send($dto->toSMSClientData())){
               $dto->status = "DELIVERED";
            }else{
               $dto->error = "SMS message not delivered by SMS Server.";
               $dto->status="FAILED";
            } 
         }
      //

      return $dto;

   }

}
