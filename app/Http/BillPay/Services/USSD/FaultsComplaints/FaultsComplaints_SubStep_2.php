<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\ClientComplaintSubTypeViewService;
use App\Http\BillPay\Services\ClientComplaintTypeViewService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_2 extends EfectivoPipelineWithBreakContract
{

    private $cCSubTypeViewService;
    private $cCTypeViewService;
    public function __construct(ClientComplaintSubTypeViewService $cCSubTypeViewService,
        ClientComplaintTypeViewService $cCTypeViewService)
    {
        $this->cCSubTypeViewService=$cCSubTypeViewService;
        $this->cCTypeViewService=$cCTypeViewService;
    }

    protected function stepProcess(BaseDTO $txDTO)
    {

        if(\count(\explode("*", $txDTO->customerJourney))==2){
            $txDTO->stepProcessed=true;
            try {

                $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
                $cCTypeView = $this->cCTypeViewService->findOneBy([
                            'order'=>$txDTO->subscriberInput,
                            'client_id'=>$txDTO->client_id,
                        ]);

                if($cCTypeView->id){
                    $subTypes = $this->cCSubTypeViewService->findAll([
                                    'complaint_type_id'=>$cCTypeView->complaint_type_id,
                                    'client_id'=>$txDTO->client_id,
                                ]);
                    $stringMenu = $cCTypeView->name." Complaints. Enter:\n";
                    foreach ($subTypes as $value){
                        $stringMenu.=$value->order.'. '.$value->name."\n";
                    }
                    $txDTO->response = $stringMenu;
                }else{
                    throw new Exception("Complaint sub types not found", 1);
                }
            } catch (\Throwable $e) {
                if($e->getCode()==1){
                    $txDTO->errorType = "InvalidInput";
                }else{
                    $txDTO->errorType = 'SystemError';
                }
                $txDTO->error='At Retrieving complaint subtypes. '.$e->getMessage();
            }
        }
        return $txDTO;

    }

}