<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\CompositePaymentAllocationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositePaymentAllocationController extends Controller
{

   public function __construct(
		private CompositePaymentAllocationService $compositePaymentAllocationService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, string $id)
   {

      try {
         $this->response['data'] =  $this->compositePaymentAllocationService->findAll($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
