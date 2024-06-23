<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Services\Web\Auth\UserLogoutService;
use App\Http\Controllers\Controller;

class UserLogoutController  extends Controller
{

   public function __construct(
		private UserLogoutService $theService,
	)
	{}

   	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy()
	{
		
		try {
			$this->response['data'] = $this->theService->delete();
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}


}
