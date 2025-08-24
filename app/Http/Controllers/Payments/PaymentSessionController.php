<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentSessionService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSessionController extends Controller
{

	public function __construct(
		private PaymentSessionService $paymentSessionService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, Authenticatable $user)
   {

      try {
         $parameters = $this->getParameters($request);
         if(!$parameters['client_id']){
            $parameters['client_id'] = $user->client_id;
         }
         $this->response['data'] = $this->paymentSessionService->findAll($parameters );
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

      /**
    * Display a listing of the resource.
   */
  public function audited(Request $request, Authenticatable $user)
  {

     try {
        $parameters = $this->getParameters($request);
        if(!$parameters['client_id']){
           $parameters['client_id'] = $user->client_id;
        }
        $this->response['data'] = $this->paymentSessionService->auditList($parameters );
     } catch (\Throwable $e) {
        $this->response['status']['code'] = 500;
        $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json( $this->response);

  }

   public function show(Request $request,$id)
   {

      try {
         $this->response['data'] = $this->paymentSessionService->findById($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
