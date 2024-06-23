<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Services\Web\Auth\UserPasswordResetService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserPasswordResetController extends Controller
{

   private $validationRules=[
                        'username' => 'required|string'
                     ];

	public function __construct(
		private UserPasswordResetService $userPasswordResetService)
	{}

   /**
    * Store a newly created resource in storage.
   */
   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->userPasswordResetService->create($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }


   /**
    * Update the specified resource in storage.
    */
   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->userPasswordResetService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
