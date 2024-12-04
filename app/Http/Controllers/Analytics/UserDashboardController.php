<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\UserDashboardService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{

	public function __construct(
		private UserDashboardService $userDashboardService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, Authenticatable $user)
   {

      try {
         $criteria = $this->getParameters($request);
         $criteria['revenueCollectorCode'] = $user->revenueCollectorCode;
         $criteria['fullnames'] = $user->fullnames;
         $criteria['client_id'] = $user->client_id;
         $this->response['data']=$this->userDashboardService->findAll($criteria);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
