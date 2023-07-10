<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentWithReceiptToDeliverBatchService;
use App\Http\Controllers\CRUDIndexController;
use Illuminate\Http\Request;

class PaymentWithReceiptToDeliverBatchController extends CRUDIndexController
{

    public function __construct(PaymentWithReceiptToDeliverBatchService $theService)
    {
        parent::__construct($theService);
    }

    //override OF INDEX
    public function index(Request $request){
        try {
            $transactions=$this->theService->findAll($request->all());
            if (\sizeof($transactions) > 0) {
                $this->response['data']= "Batch receipt delivery job initiated.\n\n Check status after a few minutes!";
            } else {
                $this->response['data']= 'No records found!';
            }
        } catch (\Throwable $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json( $this->response);
    }

}
