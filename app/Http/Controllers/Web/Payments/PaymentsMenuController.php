<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentsMenuService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsMenuController extends Controller
{
   
	public function __construct(
		private PaymentsMenuService $paymentsMenuService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request) 
   {

      try {
         $this->response['data'] = $this->paymentsMenuService->findAll(['urlPrefix' => $request->input('urlPrefix')]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
