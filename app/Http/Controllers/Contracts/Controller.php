<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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

}
