<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserOfClientController extends Controller
{

   private $validationRules=[
      'client_id' => 'required|string'
   ];

   private $theService;
   public function __construct(UserOfClientService $theService)
   {
      $this->theService=$theService;
   }

   public function index(Request  $request){

      try {
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->findAll($request->all());
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);
      
   }

}
