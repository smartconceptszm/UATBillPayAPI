<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Services\External\BillingClients\BillingClientBinderService;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\ClientService; 
use App\Http\BillPay\DTOs\BaseDTO;

class Step_IdentifyClient extends EfectivoPipelineContract
{
    
    private $clientService;
    private $binderService;
    public function __construct(BillingClientBinderService $binderService,
        ClientService $clientService)
    {
        $this->clientService= $clientService;
        $this->binderService=$binderService;
    }
    
    protected function stepProcess(BaseDTO $txDTO)
    {

        if($txDTO->error==''){ 
            try {
                $client = $this->clientService->findOneBy(['urlPrefix'=>$txDTO->urlPrefix]);
                $txDTO->client_id = $client->id;
                $txDTO->clientCode = $client->code;
                $txDTO->clientSurcharge = $client->surcharge;
                if($client->mode!='UP'){
                    $txDTO->error='System in Maintenance Mode';
                    $txDTO->errorType="MaintenanceMode";
                    return $txDTO;
                }
                if($client->status!='ACTIVE'){
                    $txDTO->error = 'Client is blocked';
                    $txDTO->errorType="ClientBlocked";
                    return $txDTO;
                }
                //Bind selected Billing Client to the Interface
                $this->binderService->bind($txDTO->urlPrefix);
            } catch (\Exception $e) {
                $txDTO->error ='At get client details from DB .'.$e->getMessage();
                $txDTO->errorType = 'SystemError';
            }
        }
        return $txDTO;
        
    }

}

