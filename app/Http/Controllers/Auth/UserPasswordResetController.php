<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\UserPasswordResetService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserPasswordResetController extends Controller
{

   private $validationRules=[
                        'username' => 'required|string'
                     ];

	public function __construct(
		private UserPasswordResetService $theService)
	{}

   /**
    * Store a newly created resource in storage.
   */
   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->create($request->all());
      } catch (\Exception $e) {
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
         $this->response['data'] = $this->theService->update($request->all(),$id);
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
