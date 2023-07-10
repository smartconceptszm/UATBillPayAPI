<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentWithReceiptToDeliverService;
use App\Http\Controllers\CRUDUpdateController;
use App\Http\BillPay\DTOs\MoMoDTO;
use Illuminate\Http\Request;

class PaymentWithReceiptToDeliverController extends CRUDUpdateController
{

    public function __construct(PaymentWithReceiptToDeliverService $theService)
    {
        parent::__construct($theService);
    }

    //override OF INDEX
    public function update(Request $request,$id){
        try {
            $record=$this->theService->update($request->all(),$id );
            if ($record->receipt!='') {
                $this->response['data']= $record->receipt;
            } else {
                $this->response['data']= $record->error;
            }
        } catch (\Throwable $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json( $this->response);
    }

}
