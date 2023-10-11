<?php

namespace App\Http\Controllers\Web;

use App\Http\Services\Web\WebPaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class WebPaymentController extends Controller
{

   private $urlPrefix;
   private $validationRules = [
      'accountNumber' => 'required|string',
      'mobileNumber' => 'required|string',
      'paymentAmount' => 'required',
      'mno_id' => 'required',
      'menu' => 'required',
   ];

	public function __construct(private Request $request)
	{
      $this->getUrlPrefix($request);
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$this->urlPrefix);
   }

   /**
    * Display the specified resource.
      */
   public function show(WebPaymentService $theService,string $accountNumber)
   {

      try {
         $this->response['data'] = $theService->getCustomer($accountNumber, $this->urlPrefix);
      } catch (\Exception $e) {
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
         $params = $request->all();
         $params['urlPrefix'] = $this->urlPrefix;
         $params['channel'] = 'WEBSITE';
         $this->response['data'] = $theService->initiateWebPayement($params);
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   private function getUrlPrefix(Request $request):void
   {
      $requestUrlArr = \explode("/",$request->url());
      if ($request->isMethod('post')) {
         $this->urlPrefix = $requestUrlArr[\count($requestUrlArr)-2];
      }else{
         $this->urlPrefix = $requestUrlArr[\count($requestUrlArr)-3];
      }
   }
   
}
