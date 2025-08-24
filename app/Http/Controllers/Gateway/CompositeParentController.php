<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\CompositeParentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositeParentController extends Controller
{
   
	public function __construct(
		private CompositeParentService $compositeParentService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function show(Request $request) 
   {

      try {
         $this->response['data'] = $this->compositeParentService->findOneBy([
                                          'customerAccount' => $request->input('customerAccount'),
                                          'client_id' => $request->input('client_id')
                                       ]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
