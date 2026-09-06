<?php

namespace App\Http\Middleware;


use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

use Closure;

class PaymentViaAPIMiddleware
{

    public function __construct(
        private ClientWalletService $clientWalletService)
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
        $wallet = $this->clientWalletService->findOneBy([
                                                'payments_provider_id' => $user->payments_provider_id,
                                                'client_id' => $user->service_provider_id 
                                            ]);
                                            
        App::bind(
			 \App\Http\Services\PublicAPI\IInitiateAPIPayment::class, 
			 $wallet->paymentMethod
		);


        return $next($request);

    }

    public function terminate($request, $response)
    {
        
    }
    
}
