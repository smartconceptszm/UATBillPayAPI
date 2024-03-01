<?php

namespace App\Http\Controllers\Clients;

use App\Http\Services\Clients\ClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{

   protected  $validationRules=[
                  'name' => 'required|string|unique:clients',
                  'shortName' => 'required|string|unique:clients',
                  'urlPrefix' => 'required|string|unique:clients'
               ];
	public function __construct(
		private ClientService $theService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->theService->findAll($request->query());
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

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->create($this->getParameters($request));
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
         $this->response['data'] = $this->theService->findById($id);
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
         $this->response['data'] = $this->theService->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->theService->update($this->getParameters($request),$id);
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
         $this->response['data'] = $this->theService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
