<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\External\SMSClients\ISMSClient;
use App\Http\BillPay\Services\MnoChargeService;
use App\Http\BillPay\Services\MessageService;
use App\Http\BillPay\Services\ClientService;
use App\Http\BillPay\Services\Enums\MNOs;   
use App\Http\BillPay\Services\MnoService;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Facades\DB;
use Exception;

class SMSService
{

    private $mnoChargeService;
    private $messageService;
    private $clientService;
    private $mnoService;
    private $smsClient;
    public function __construct(MessageService $messageService, ISMSClient $smsClient, 
        MnoChargeService $mnoChargeService, MnoService $mnoService, 
        ClientService $clientService)
    {
        $this->mnoChargeService = $mnoChargeService;
        $this->messageService = $messageService;
        $this->clientService = $clientService;
        $this->mnoService=$mnoService;
        $this->smsClient = $smsClient;
    }

    public function send(BaseDTO $dto):BaseDTO|null
    {

        try {
            //Get client details
            $dto = $this->getClient($dto);
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
        } catch (\Exception $e) {
            if($e->getCode() == 1){
                $dto->error = $e->getMessage();
            }else{
                $dto->error = 'Error at  SMSService@create. '.$e->getMessage();
            }
            $dto->status="FAILED";
        }
        return $dto;

    }

    private function getClient(BaseDTO $dto):BaseDTO|null
    {

        $client = $this->clientService->findById($dto->client_id);
        $dto->smsPayMode = $client->smsPayMode;
        $dto->urlPrefix = $client->urlPrefix;
        $dto->shortName = $client->shortName;
        $dto->balance = $client->balance;
        return $dto;

    }

    private function getCharges(BaseDTO $dto):BaseDTO|null
    {

        //Get SMS Charge
            if(!$dto->mno_id){
                $mnoName = MNOs::getMNO(\substr($dto->mobileNumber,0,5));
                $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);
                $dto->mno_id = $mno->id;
            }
            $mnoCharges = $this->mnoChargeService->findOneBy([
                                    'client_id'=>$dto->client_id,
                                    'mno_id'=> $dto->mno_id
                                ]);
            $dto->smsCharge = (float)$mnoCharges->smsCharge;
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
                    $dto->status="DELIVERED";
                }else{
                    $dto->error="SMS message not delivered by SMS Server.";
                    $dto->status="FAILED";
                } 
            }
        //

        return $dto;

    }

}
