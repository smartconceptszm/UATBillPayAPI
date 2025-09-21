<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentsAuditedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsAuditedController extends Controller
{

	public function __construct(
		private PaymentsAuditedService $paymentsAuditedService)
	{}

   /**
    * Display a listing of the resource.
    */
   public function index(Request $request)
   {

      try {
         $this->response['data']= $this->paymentsAuditedService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function trail(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->paymentsAuditedService->updateHistory($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
