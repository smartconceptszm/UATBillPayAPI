<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\CompositePaymentsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositePaymentController extends Controller
{

	public function __construct(
		private CompositePaymentsService $compositePaymentsService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->compositePaymentsService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   /**
    * Display the specified resource.
   */
   public function show(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->compositePaymentsService->findById($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
