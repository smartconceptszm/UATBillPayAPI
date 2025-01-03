<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Gateway\CustomerService;
use App\Http\Services\Clients\ClientService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

	public function __construct(
      private ClientMenuService $clientMenuService,
      private ClientService $clientService,
      private Request $request)
	{
      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $client = $this->clientService->findById($request->input('client_id'));
      $billingClient = 'MockBillingClient';
      if($billpaySettings['USE_BILLING_MOCK_'.strtoupper($client->urlPrefix)]!="YES"){
         $clientMenuService = $this->clientMenuService->findById($request->input('menu_id'));
         $billingClient =  $clientMenuService->billingClient;
      }
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
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
