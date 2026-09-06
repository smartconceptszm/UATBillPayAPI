<?php

namespace App\Http\Middleware;

use App\Http\Services\PublicAPI\PaymentsViaAPIMenuService;
use App\Http\Services\Utility\SCLExternalServiceBinder;
use Illuminate\Support\Facades\Auth;



use Closure;

class CustomerViaAPIMiddleware
{

    public function __construct(
        private SCLExternalServiceBinder $sclExternalServiceBinder,
        private PaymentsViaAPIMenuService $paymentsMenusService)
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

        $clientMenus = $this->paymentsMenusService->findAll();
        $clientMenu = $clientMenus[0];
        $this->sclExternalServiceBinder->bindBillingClient($user->urlPrefix,$clientMenu->id);

        return $next($request);

    }

    public function terminate($request, $response)
    {
        
    }
    
}
