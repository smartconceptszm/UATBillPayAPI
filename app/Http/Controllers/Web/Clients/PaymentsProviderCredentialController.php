<?php

namespace App\Http\Controllers\Web\Clients;

use App\Http\Services\Web\Clients\PaymentsProviderCredentialService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsProviderCredentialController extends Controller
{
   
   protected $validationRules = [
               'client_id' => 'required',
               'key' => 'required',
               'keyValue' => 'required'
            ];
	public function __construct(
		private PaymentsProviderCredentialService $paymentsProviderCredentials)
	{}


   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentsProviderCredentials->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }


   /**
    * Display the specified resources.
   */
  public function credentialsofpaymentsprovider(Request $request, string $id)
  {

     try {
        $this->response['data'] = $this->paymentsProviderCredentials->findAll(['payments_provider_id'=>$id]);
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
         $this->response['data'] = $this->paymentsProviderCredentials->create($this->getParameters($request));
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
         $this->response['data'] = $this->paymentsProviderCredentials->findById($id);
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
         $this->response['data'] = $this->paymentsProviderCredentials->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->paymentsProviderCredentials->update($this->getParameters($request),$id);
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
         $this->response['data'] = $this->paymentsProviderCredentials->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
