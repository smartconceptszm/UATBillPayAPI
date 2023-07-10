<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CRUDIndexController extends Controller
{

   protected $theService;
   public function __construct(IFindAllService $theService)
   {
      $this->theService=$theService;
   }
                  
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request  $request){

      try {
         $this->response['data']=$this->theService->findAll($request->all());
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);
      
   }

}
