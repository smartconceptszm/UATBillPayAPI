<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Gateway\CustomerService;
use App\Http\Services\Clients\ClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

	public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder,
      private ClientService $clientService,
      private Request $request)
	{
      $client = $this->clientService->findById($request->input('client_id'));
      $this->sclExternalServiceBinder->bindBillingClient($client->urlPrefix,$request->input('menu_id'));
   }

   /**
    * Display the specified resource.
      */
   public function show(Request $request, CustomerService $customerService)
   {

      try {
         $this->response['data'] = $customerService->getCustomer($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
