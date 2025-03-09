<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\DTOs\BaseDTO;
use Exception;

class UserLoginService
{

	/**
	 * Get a JWT via given credentials.
	 *
	 * @param  BaseDTO  $dto
	 * @return BaseDTO
	 */
	public function create(BaseDTO $dto): BaseDTO
	{

		try {
			if (!$token = Auth::attempt($dto->credentials())) {
					throw new Exception("Invalid username and/or password. Try again!", 1);
			}
			$user = Auth::user(); 
			$dto->expires_in = Auth::factory()->getTTL()*60;
			$dto->revenueCollectorCode = $user->revenueCollectorCode;
			$dto->fullnames = $user->fullnames;
			$dto->client_id = $user->client_id;
			$dto->urlPrefix = $user->urlPrefix;
			$dto->client = $user->client;
			$dto->rights = $user->rights;
			$dto->token_type = 'bearer';
			$dto->token = $token;
			$dto->id = $user->id;
			return  $dto;
		} catch (\Throwable $e) {
			throw new Exception($e->getMessage());
		}

	}

}
