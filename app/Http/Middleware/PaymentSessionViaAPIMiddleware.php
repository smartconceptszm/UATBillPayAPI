<?php

namespace App\Http\Middleware;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Auth\APIClientService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Auth;



use Closure;

class PaymentSessionViaAPIMiddleware
{

    public function __construct(
        private SCLExternalServiceBinder $sclExternalServiceBinder,
        private ClientWalletService $clientWalletService,
        private APIClientService $apiClientService,
        private ClientService $clientService)
    {}
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = Auth::user();

        $client = $this->clientService->findById($user->service_provider_id);

        $wallet = $this->clientWalletService->findOneBy([
                                        'payments_provider_id' => $user->payments_provider_id,
                                        'client_id' => $client->id 
                                    ]);

        $APIClient = $this->apiClientService->findById($user->api_client_id);

        $this->sclExternalServiceBinder->bindBillingClient($client->urlPrefix,$request->menu_id);

        $request->merge([
                            'payments_provider_id' => $user->payments_provider_id,
                            'clientSurcharge' => $client->surcharge,
                            'paymentMethod' => $wallet->paymentMethod,
                            'walletHandler' => $wallet->handler,
                            'testMSISDN' => $client->testMSISDN,
                            'shortCode' => $client->shortCode,
                            'urlPrefix' => $client->urlPrefix,
                            'channel' => $APIClient->channel,
                            'client_id' => $client->id,
                            'wallet_id' => $wallet->id
                        ]);

        return $next($request);

    }

    public function terminate($request, $response)
    {
        
    }
    
}
