<?php

namespace App\Http\Controllers\Contracts;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\Controllers\Contracts\Controller;
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

}
