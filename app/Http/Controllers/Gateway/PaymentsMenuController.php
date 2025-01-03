<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\PaymentsMenuService;
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
         $this->response['data'] = $this->paymentsMenuService->findAll(['client_id' => $request->input('client_id')]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function submenus(Request $request) 
   {

      try {
         $this->response['data'] = $this->paymentsMenuService->submenus(['parent_id' => $request->input('parent_id')]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
