<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentsProviderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsProviderController extends Controller
{
   
	public function __construct(
		private PaymentsProviderService $paymentsProviderService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->paymentsProviderService->findAll(['urlPrefix' => $request->input('urlPrefix')]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
