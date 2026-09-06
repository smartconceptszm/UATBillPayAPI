<?php

namespace App\Http\Services\Auth;

use App\Http\Services\PublicAPI\PaymentsViaAPIMenuService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use App\Http\DTOs\BaseDTO;
use Exception;

class APIUserLoginService
{

    public function __construct(
        private PaymentsViaAPIMenuService $paymentMenuService)
    {}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @param  BaseDTO  $dto
	 * @return array
	 */
	public function create(BaseDTO $dto): array
	{

		if (!$token = Auth::guard('apiuser')->attempt($dto->credentials())) {
				throw new AuthenticationException("Invalid username and/or password. Try again!");
		}

		$user = Auth::guard('apiuser')->user(); 

		$defaultPaymentMenu= $this->paymentMenuService->getDefaultMenu($user);

		return  [
						'id'				=> $user->id,
						'username' 		=> $user->username,
						'menu_id'		=>	$defaultPaymentMenu->id,
						'prompt'			=>	$defaultPaymentMenu->prompt,
						'token'			=> $token,
						'token_type'	=> 'bearer',
						'expires_in'	=> Auth::factory()->getTTL()*60
					];

	}

}
