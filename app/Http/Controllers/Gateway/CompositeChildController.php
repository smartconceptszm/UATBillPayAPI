<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\CompositeChildService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositeChildController extends Controller
{
   
	public function __construct(
		private CompositeChildService $compositeChildService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request) 
   {

      try {
         $this->response['data'] = $this->compositeChildService->findAll([
                                          'client_id' => $request->input('client_id'),
                                          'parentAccount' => $request->input('parentAccount')
                                       ]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
