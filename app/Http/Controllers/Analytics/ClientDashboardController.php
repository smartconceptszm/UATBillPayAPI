<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\ClientDashboardService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{

	public function __construct(
		private ClientDashboardService $clientDashboard)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, Authenticatable $user)
   {

      try {
         $criteria = $this->getParameters($request);
         if(!$criteria['client_id']){
            $criteria['client_id'] = $user->client_id;
         }
         $this->response['data']=$this->clientDashboard->findAll($criteria);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
