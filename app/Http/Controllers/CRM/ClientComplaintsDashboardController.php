<?php

namespace App\Http\Controllers\CRM;

use App\Http\Services\CRM\ClientComplaintsDashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientComplaintsDashboardController extends Controller
{

	public function __construct(
		private ClientComplaintsDashboardService $complaintDashboardService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->complaintDashboardService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
