<?php

namespace App\Http\Controllers\PublicAPI;

use App\Http\Services\PublicAPI\CustomerService;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{

   //  public function __construct(
   //      private )
   //  {}

   /**
    * Display the specified resource.
      */
   public function show(CustomerService $customerService,string $customerAccount)
   {

      try {
         $this->response['data'] = $customerService->getCustomer(\strtoupper($customerAccount));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
