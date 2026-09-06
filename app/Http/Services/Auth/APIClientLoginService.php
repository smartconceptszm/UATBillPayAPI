<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Clients\PaymentsProviderService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use App\Http\DTOs\BaseDTO;
use Exception;

class APIClientLoginService
{


	public function __construct(
      private PaymentsProviderService $paymentsProviderService,
		private ClientService $clientService
   ) {}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @param  BaseDTO  $dto
	 * @return array 
	 */
	public function create(BaseDTO $dto): array
	{

		if (!$token = Auth::guard('apiclient')->attempt($dto->credentials())) {
			throw new AuthenticationException("Invalid username and/or password. Try again!");
		}

		$user = Auth::guard('apiclient')->user(); 

		$response = [
							'id'							=> $user->id,
							'category' 					=> $user->category,							
							'username' 					=> $user->username,
							'name'						=> $user->name,
							'email'						=> $user->email,
							'token'						=> $token,
							'token_type'				=> 'bearer',
							'expires_in'				=> Auth::factory()->getTTL()*60,
							'payments_provider_id'	=> "",
							'service_provider_id'		=> "",
							'paymentsProvider'		=> "",
							'serviceProvider'			=> "",
							'mobileNumber' 			=> $user->mobileNumber,
							'shortName' 				=> $user->shortName,
							'channel' 					=> $user->channel
						];

		if($user->service_provider_id){
			$client = $this->clientService->findById($user->service_provider_id);
			$response['service_provider_id'] = $client->id;
			$response['serviceProvider'] = $client->name;
		}

		if($user->payments_provider_id){
			$paymentsProvider = $this->paymentsProviderService->findById($user->payments_provider_id);
			$response['payments_provider_id'] = $paymentsProvider->id;
			$response['paymentsProvider'] = $paymentsProvider->name;
		}

		return  $response;

	}

}
