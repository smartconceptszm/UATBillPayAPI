<?php

namespace App\Http\Controllers\Contracts;

use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class CRUDCreateController extends Controller
{

   protected $theService;
   public function __construct(ICreateService $theService)
   {
      $this->theService = $theService;
   }
                              
   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request  $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data']=$this->theService->create($request->all());
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
