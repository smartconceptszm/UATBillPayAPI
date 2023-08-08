<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentFailedService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class PaymentFailedController extends Controller
{

   private $theService;
   public function __construct(PaymentFailedService $theService)
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
         $serviceResponse = $this->theService->findAll($params,$arrFields);
         $this->response['data'] = $serviceResponse['data'];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->theService->update($request->all(),$id);
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
