<?php

namespace App\Http\Controllers\Clients;

use App\Http\Services\Clients\MnosOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MnosOfClientController extends Controller
{
   

	public function __construct(
		private MnosOfClientService $mnosOfClient)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->mnosOfClient->findAll($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
