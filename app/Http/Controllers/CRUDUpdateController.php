<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CRUDUpdateController extends Controller
{

   protected $theService;
   public function __construct(IUpdateService $theService)
   {
      $this->theService = $theService;
   }
                           
   /**
    * Update the specified resource in storage.
    * @param  id
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {

      try {
         $this->response['data']=$this->theService->update($request->all(),$id);
      } catch (\Exception $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json($this->response);

   }
                
}
