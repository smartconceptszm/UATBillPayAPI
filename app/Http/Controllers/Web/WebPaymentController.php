<?php

namespace App\Http\Controllers\Web;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Web\WebPaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class WebPaymentController extends Controller
{

   private $validationRules = [
                                 'mobileNumber' => 'required|string',
                                 'paymentAmount' => 'required',
                                 'mno_id' => 'required',
                                 'menu_id' => 'required',
                              ];

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
      App::bind(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$billingEnquiryClient);
   }

   /**
    * Display the specified resource.
      */
   public function show(Request $request, WebPaymentService $theService)
   {

      try {
         $this->response['data'] = $theService->getCustomer($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
 * Store a newly created resource in storage.
   */
   public function store(Request $request, WebPaymentService $theService)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $params = $this->getParameters($request);
         $params['channel'] = 'WEBSITE';
         $this->response['data'] = $theService->initiateWebPayement($params);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
