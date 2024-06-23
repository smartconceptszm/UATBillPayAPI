<?php

namespace App\Http\Services\Web\Auth;

use Illuminate\Support\Facades\Auth;
use Exception;

class UserLogoutService
{

	/**
	 * Invalidate the JWT for login user.
	 */
	public function delete():bool
	{

		try {
			Auth::logout();
			return true;
		} catch (\Throwable $e) {
			throw new Exception($e->getMessage());
		}

	}

}
