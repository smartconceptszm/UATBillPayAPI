<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Contracts\CRUDController;
use App\Http\BillPay\Services\Auth\UserService;

class UserController extends CRUDController
{

   protected $validationRules=[
      'mobileNumber' => 'required|string|size:12|unique:users',
      'username' => 'required|string|unique:users',
      'client_id' => 'required|string',
      'fullnames' => 'required|string',
      'password' => 'required|string',
   ];

   public function __construct(UserService $theService)
   {
      parent::__construct($theService);
   }

}