<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
   
   use AuthorizesRequests, ValidatesRequests;

   protected $response = [
      'status'=>[
         'code' => 200,
         'message' => 'OK'
         ],
      'data'=>[]
   ];

   protected function getParameters(Request $request,) : array {
      $theURIKey='/'.$request->path();
      $requestParameters = $request->all();
      if(\key_exists($theURIKey,$requestParameters)){
         unset($requestParameters[$theURIKey]);
      }
      return $requestParameters;
   }

}
