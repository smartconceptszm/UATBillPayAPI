<?php

namespace App\Http\Controllers\Clients;

use App\Http\Services\Clients\AggregatedClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AggregatedClientController extends Controller
{

   protected  $validationRules=[
                  'parent_id' => 'required|string',
                  'client_id' => 'required|string',
                  'menuNo' => 'required|string'
               ];
	public function __construct(
		private AggregatedClientService $aggregatedClientService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->aggregatedClientService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   /**
    * Display the specified resources.
   */
   public function clientsofaggregator(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->aggregatedClientService->getClientsOfAggregator($id);
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
         $this->response['data'] = $this->aggregatedClientService->create($this->getParameters($request));
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
         $this->response['data'] = $this->aggregatedClientService->findById($id);
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
         $this->response['data'] = $this->aggregatedClientService->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->aggregatedClientService->update($this->getParameters($request),$id);
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
         $this->response['data'] = $this->aggregatedClientService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
