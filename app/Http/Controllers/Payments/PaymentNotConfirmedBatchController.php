<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentNotConfirmedBatchService;
use App\Http\Controllers\Contracts\CRUDIndexController;
use Illuminate\Http\Request;

class PaymentNotConfirmedBatchController extends CRUDIndexController
{

   public function __construct(PaymentNotConfirmedBatchService $theService)
   {
      parent::__construct($theService);
   }

   //override OF INDEX
   public function index(Request $request){
      try {
         $transactions=$this->theService->findAll($request->all());
         if (\sizeof($transactions) > 0) {
               $this->response['data']= "Batch payment review job initiated.\n\n Check status after a few minutes!";
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
