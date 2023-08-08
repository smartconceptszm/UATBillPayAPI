<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\BatchPaymentReceiptService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class BatchPaymentReceiptController extends Controller
{

   private  $validationRules = [
                                 'dateFrom' => 'required|string',
                                 'dateTo' => 'required|string'
                              ];
   private $theService;
   public function __construct(BatchPaymentReceiptService $theService)
   {
      $this->theService = $theService;
   }

   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $response = $this->theService->create($request->all());
         $this->response['data'] = $response->data;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
