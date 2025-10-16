<?php

namespace App\Http\Services\SMS;



use App\Http\Services\Clients\ClientSMSChannelService;
use App\Http\Services\Clients\SMSProviderService;
use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\SMS\MessageService;
use App\Jobs\SMSAnalyticsDailySingleJob;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SMSAnalyticsRegularJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Enums\MNOs;
use Illuminate\Support\Carbon;
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
         if(!$dto->handler){
            $dto = $this->getSMSChannel($dto);
         }
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
            Log::error('Error at creating SMS record in database. '.$e->getMessage());
            DB::rollBack();
         }
         $dto->created_at = $sms->created_at;
         $dto->status = $sms->status;
         $dto->id = $sms->id;

         //Do Analytics
         $this->dispatchAnalyticsJobs($dto);

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

   private function dispatchAnalyticsJobs(BaseDTO $dto)
   {

      if($dto->status =='DELIVERED'){
         //Regular Analytics
         SMSAnalyticsRegularJob::dispatch($dto)
                                 ->delay(Carbon::now()->addSeconds(1))
                                 ->onQueue('UATlow');

         //Daily Analytics
         $yesterday = Carbon::yesterday()->toDateString();
         $lastDailyAnalytics = Cache::get('DATE_OF_LAST_SMS_DAILY_ANALYTICS');
         if($lastDailyAnalytics  != $yesterday ){
            Cache::put('DATE_OF_LAST_SMS_DAILY_ANALYTICS',$yesterday,Carbon::now()->addHours(24));
            SMSAnalyticsDailySingleJob::dispatch(Carbon::yesterday())
                                             ->delay(Carbon::now()->addSeconds(1))
                                             ->onQueue('UATlow');
         }
      }

   }

}
