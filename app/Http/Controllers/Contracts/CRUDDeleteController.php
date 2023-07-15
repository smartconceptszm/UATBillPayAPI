<?php

namespace App\Http\Controllers\Contracts;

use App\Http\BillPay\Services\Contracts\IDeleteService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class CRUDDeleteController extends Controller
{

   protected $theService;
   public function __construct(IDeleteService $theService)
   {
      $this->theService = $theService;
   }
                              
   /**
    * Remove the specified resource from storage.
* @param  \Illuminate\Http\Request  $request
   * @param  string Model id
   * @return \Illuminate\Http\Response
   */
   public function destroy(Request $request, $id)
   {

      try {
         $this->response['data']=$this->theService->delete($id);
      } catch (\Exception $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json($this->response);

   }

}
