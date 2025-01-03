<?php

namespace App\Http\Controllers\Clients;

use App\Http\Services\Clients\MenusOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenusOfClientController extends Controller
{
   
	public function __construct(
		private MenusOfClientService $menusOfClientService)
	{}


   public function levelOneMenus(string $id)
   {

      try {
         $this->response['data'] = $this->menusOfClientService->levelOneMenus($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function subMenus(string $id)
   {

      try {
         $this->response['data'] = $this->menusOfClientService->subMenus($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
