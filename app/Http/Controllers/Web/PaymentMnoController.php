<?php

namespace App\Http\Controllers\Web;

use App\Http\Services\Web\PaymentMnoService;
use App\Http\Services\Clients\ClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMnoController extends Controller
{
   
	public function __construct(
		private PaymentMnoService $theService,
      private ClientService $clientService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $requestUrlArr = \explode("/",$request->url());
         $client = $this->clientService->findOneBy([
                           'urlPrefix' => $requestUrlArr[\count($requestUrlArr)-2]
                        ]);
         $this->response['data'] = $this->theService->findAll($client->id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
