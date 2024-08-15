<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Payments\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

	public function __construct(
      private ClientMenuService $clientMenuService,
      private Request $request)
	{
      $billingClient = 'MockBillingClient';
      if(\env('USE_BILLING_MOCK')!="YES"){
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
