<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Exception;

class UserLogoutService
{

	/**
	 * Inavlidate the JWT for login user.
	 */
	public function delete(string $id):bool
	{

		try {
			Auth::logout();
			return true;
		} catch (\Exception $e) {
			throw new Exception($e->getMessage());
		}

	}

}
