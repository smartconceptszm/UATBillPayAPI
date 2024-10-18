<?php

namespace App\Http\Controllers\Clients;

use App\Http\Services\Clients\ClientRevenueCodeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientRevenueCodeController extends Controller
{
   
   protected $validationRules = [
               'menu_id' => 'required',
               'code' => 'required',
               'name' => 'required'
            ];
	public function __construct(
		private ClientRevenueCodeService $clientRevenueCodeService)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->clientRevenueCodeService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }


   /**
    * Display the specified resources.
   */
  public function revenueCodesOfMenu(Request $request, string $menu_id)
  {

     try {
        $this->response['data'] = $this->clientRevenueCodeService->findAll(['menu_id'=>$menu_id]);
     } catch (\Throwable $e) {
           $this->response['status']['code'] = 500;
           $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json($this->response);

  }



   /**
    * Store a newly created resource in storage.
      */
   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->clientRevenueCodeService->create($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Display the specified resource.
      */
   public function show(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->clientRevenueCodeService->findById($id);
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
         $this->response['data'] = $this->clientRevenueCodeService->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->clientRevenueCodeService->update($this->getParameters($request),$id);
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
         $this->response['data'] = $this->clientRevenueCodeService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
