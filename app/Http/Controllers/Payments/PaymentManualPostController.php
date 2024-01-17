<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentManaulPostService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentManualPostController extends Controller
{

   private $validationRules = [
                     'mnoTransactionId' => 'required|string',
                     'id' => 'required'
                  ];
	public function __construct(
		private PaymentManaulPostService $theService)
	{}


   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $response = $this->theService->create($request->all());
         $this->response['data'] = $response->data;

      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
