<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentNotReceiptedService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class PaymentNotReceiptedController extends Controller
{

   private $theService;
   public function __construct(PaymentNotReceiptedService $theService)
   {
      $this->theService = $theService;
   }

   /**
    * Display a listing of the resource.
    */
   public function index(Request $request)
   {

      try {
         $params = $request->all();
         $arrFields = ['*'];
         if(\Arr::exists($params, 'fieldList')){
            $arrFields = explode(',', $params['fieldList']);
            \unset($params['fieldList']);
         }
         $this->response['data']=$this->theService->findAll($params,$arrFields);
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);

   }

}
