<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\PromotionService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromotionController extends Controller
{

	public function __construct(
		private PromotionService $promotionService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request, Authenticatable $user)
   {

      try {
         $params = $request->query();
         $params['client_id'] = $user->client_id;
         $this->response['data'] =  $this->promotionService->findAll($params);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   /**
    * Store a newly created resource in storage.
      */
   public function store(Request $request)
   {

   }

   /**
    * Display the specified resource.
      */
   public function show(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->promotionService->findById($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Display one resource.
      */
   public function findOneBy(Request $request)
   {

      try {
         $this->response['data'] = $this->promotionService->findOneBy($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Update the specified resource in storage.
      */
   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->promotionService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Remove the specified resource from storage.
      */
   public function destroy(string $id)
   {
      
      try {
         $this->response['data'] = $this->promotionService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
