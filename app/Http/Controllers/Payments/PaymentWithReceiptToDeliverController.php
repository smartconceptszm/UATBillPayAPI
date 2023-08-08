<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentWithReceiptToDeliverService;
use App\Http\Controllers\Contracts\CRUDUpdateController;
use Illuminate\Http\Request;

class PaymentWithReceiptToDeliverController extends CRUDUpdateController
{

   public function __construct(PaymentWithReceiptToDeliverService $theService)
   {
      parent::__construct($theService);
   }

   //Override of Update 
   public function update(Request $request,$id){
      try {
         $record = $this->theService->update($request->all(),$id );
         if ($record->receipt!='') {
               $this->response['data']= $record->receipt;
         } else {
               $this->response['data']= $record->error;
         }
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);
   }

}
