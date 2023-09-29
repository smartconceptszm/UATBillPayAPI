<?php

namespace App\Http\Controllers\Web;

use App\Http\Services\Web\PaymentMenuService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMenuController extends Controller
{
   
	public function __construct(
		private PaymentMenuService $theService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $requestUrlArr = \explode("/",$request->url());
         $this->response['data'] = $this->theService->findAll([
                                       'urlPrefix' => $requestUrlArr[\count($requestUrlArr)-2],
                                    ]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
