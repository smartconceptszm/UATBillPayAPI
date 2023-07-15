<?php

namespace App\Http\Controllers\SMS;

use App\Http\BillPay\Services\SMS\MessageService;
use App\Http\Controllers\Contracts\Controller;

class MessageController extends Controller
{

   protected $theService;
   public function __construct(MessageService $theService)
   {
      $this->theService = $theService;
   }
                  
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request)
   {

   try {
      $params = $request->all();
      $arrFields = ['*'];
      if(\Arr::exists($params, 'fieldList')){
         $arrFields = explode(',', $params['fieldList']);
         \unset($params['fieldList']);
      }
      $this->response['data']=$this->theService->findAll($params,$arrFields);
   } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
   }
   return response()->json( $this->response);

   }
   
      /**
    * Display the specified resource.
    *@param  \Illuminate\Http\Request  $request
   * @param  id
   * @return \Illuminate\Http\Response
   */
   public function show(Request $request, string $id)
   {

      try {
         $params = $request->all();
         $arrFields = ['*'];
         if(\Arr::exists($params, 'fieldList')){
            $arrFields = explode(',', $params['fieldList']); 
         }
         $this->response['data'] = $this->theService->findById($id,$arrFields);
      } catch (\Exception $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
      }
      return response()->json($this->response);

   }
 
      

}
