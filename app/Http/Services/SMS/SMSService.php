<?php

namespace App\Http\Services\SMS;

use App\Http\Services\Clients\ClientSMSChannelService;
use App\Http\Services\Clients\SMSProviderService;
use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\SMS\MessageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;  
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;
use Exception;

class SMSService
{

   private $channelChargeable = false;

   public function __construct(
      private ClientSMSChannelService $clientSMSChannelService,
      private SMSProviderService $smsProviderService,
      private ClientMnoService $clientMnoService, 
      private MessageService $messageService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private SMSTxDTO $smsTxDTO)
   {}

   public function send(BaseDTO $dto):BaseDTO|null
   {

      try {
         if($dto->message == ''){
            throw new Exception("Message is empty!",1);
         }
         //Get client details
         $dto = $this->getClient($dto);
         //Get MNO details
         $dto = $this->getMNO($dto);
         //Take Care of Charges
         $dto = $this->getSMSChannel($dto);
         //Send the SMS
         $dto = $this->sendMessage($dto);
         //Log the transaction
         $this->logStatus($dto);
         // Save the Record
         DB::beginTransaction();
         try {
            $sms = $this->messageService->create($dto->toSMSMessageData());
            if($sms->status == 'DELIVERED' && $this->channelChargeable){
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
      }else{
         $client = $this->clientService->findOneBy(['shortName'=>$dto->shortName]);
      }
      $dto->smsPayMode = $client->smsPayMode;
      $dto->shortName = $client->shortName;
      $dto->urlPrefix = $client->urlPrefix;
      $dto->balance = $client->balance;
      $dto->client_id = $client->id;
      return $dto;

   }

   private function getMNO(BaseDTO $dto):BaseDTO|null
   {

      if($dto->mno_id){
         return $dto;
      }
      $mnoName = MNOs::getMNO(substr($dto->mobileNumber,0,5));
      $mno = $this->mnoService->findOneBy(['name' => $mnoName]);
      $dto->mno_id = $mno->id;
      return $dto;

   }

   private function getSMSChannel(BaseDTO $dto):BaseDTO|null
   {

      $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
      if($billpaySettings['SMS_SEND_USE_MOCK_'.strtoupper($dto->urlPrefix)] == "YES"){
         $dto->handler = 'MockSMSDelivery';
         return $dto;
      }

      if( $dto->handler = Cache::get($dto->transaction_id."_smsClient",'')){
         return $dto;
      }

      $clientMNOs = $this->clientMnoService->findOneBy([
                                                   'client_id' => $dto->client_id,
                                                   'mno_id' => $dto->mno_id
                                                ]);
      $dto->smsCharge = (float)$clientMNOs->smsCharge;
      $clientSMSChannel = $this->clientSMSChannelService->findById($clientMNOs->smsChannel);
      $smsProvider = $this->smsProviderService->findById($clientSMSChannel->sms_provider_id);
      $dto->channel_id = $clientMNOs->smsChannel;
      $dto->sms_provider_id = $smsProvider->id;
      $dto->handler = $smsProvider->handler;
      //Check if Client has enough balance
         if(($dto->smsPayMode == 'POST-PAID') || ($dto->balance > $dto->smsCharge)){
            $dto->balance = $dto->balance - $dto->smsCharge;
            $this->channelChargeable = true;
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
            //
            $smsClient = App::make($dto->handler);
            if($smsClient->send($dto->toSMSClientData())){
               $dto->status = "DELIVERED";
            }else{
               $dto->error = "SMS message not delivered by SMS Server.";
               $dto->status="FAILED";
            } 
         }
      //
      return $dto;

   }

   private function logStatus(BaseDTO $dto)
   {

      $logMessage = sprintf(
         '(%s) SMS Message %s. Type: %s. %sPhone: %s.',
         $dto->urlPrefix,
         $dto->status === 'DELIVERED'? "SENT. Message: ".$dto->message:" NOT SENT, SMS Server error",
         $dto->type,
         $dto->transaction_id? "Transaction Id: ".$dto->transaction_id." .":" ",
         $dto->mobileNumber
      );

      if ($dto->status === 'DELIVERED') {
         Log::info($logMessage);
      } else {
         Log::error($logMessage);
      }

   }

}
