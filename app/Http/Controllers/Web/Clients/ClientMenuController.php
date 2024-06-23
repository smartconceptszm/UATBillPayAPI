<?php

namespace App\Http\Controllers\Web\Clients;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientMenuController extends Controller
{
   
   protected $validationRules = [
               'client_id' => 'required',
               'order' => 'required',
               'prompt' => 'required',
               'handler' => 'required'
            ];
	public function __construct(
		private ClientMenuService $clientMenu)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->clientMenu->findAll($request->query());
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
         $this->response['data'] = $this->clientMenu->create($this->getParameters($request));
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
         $this->response['data'] = $this->clientMenu->findById($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

      /**
    * Display the specified resources.
      */
      public function menusofclient(Request $request, string $id)
      {
   
         try {
            $this->response['data'] = $this->clientMenu->findAll(['client_id'=>$id]);
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
         $this->response['data'] = $this->clientMenu->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->clientMenu->update($this->getParameters($request),$id);
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
         $this->response['data'] = $this->clientMenu->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
