<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentFailedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentFailedController extends Controller
{

	public function __construct(
		private PaymentFailedService $theService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->theService->findAll($request->all());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $response = $this->theService->update($id);
         $this->response['data'] = $response->data;
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
