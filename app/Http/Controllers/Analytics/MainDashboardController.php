<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\MainDashboardService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainDashboardController extends Controller
{

	public function __construct(
		private MainDashboardService $mainDashboard)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, Authenticatable $user)
   {

      try {
         $criteria = $this->getParameters($request);
         $this->response['data']=$this->mainDashboard->findAll($criteria);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
