<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\PromotionEntriesNotProcessedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromotionEntriesNotProcessedController extends Controller
{

	public function __construct(
		private PromotionEntriesNotProcessedService $promotionEntriesNotProcessedService)
	{}

   /**
    * Display a listing of the resource.
    */
   public function index(Request $request)
   {

      try {
         $this->response['data']= $this->promotionEntriesNotProcessedService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $params = $request->all();
         $this->response['data'] = $this->promotionEntriesNotProcessedService->processEntry($id,$params['promotion_id']);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
