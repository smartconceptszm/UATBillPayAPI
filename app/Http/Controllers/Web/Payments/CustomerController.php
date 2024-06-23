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
      $billingEnquiryClient = 'MockEnquiry';
      if(\env('USE_BILLING_MOCK')!="YES"){
         $clientMenuService = $this->clientMenuService->findById($request->input('menu_id'));
         $clientMenuService = \is_null($clientMenuService)?null:(object)$clientMenuService->toArray();
         $billingEnquiryClient = $clientMenuService->enquiryHandler;
      }
      App::bind(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$billingEnquiryClient);
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
