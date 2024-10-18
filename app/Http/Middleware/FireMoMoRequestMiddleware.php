<?php

namespace App\Http\Middleware;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\InitiatePaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;

use Closure;

class FireMoMoRequestMiddleware
{

    public function __construct(
        private PaymentsProviderCredentialService $paymentsProviderCredentialService,
        private MoMoDTO $momoDTO)
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
        return $next($request);
    }

    public function terminate($request, $response)
    {
        
        $ussdParams = $request->ussdParams;
        if ($ussdParams) {
            if($ussdParams['fireMoMoRequest']){

                $paymentDTO =  $this->momoDTO->fromSessionData($ussdParams);
                $paymentDTO->customer = \json_decode(Cache::get($paymentDTO->urlPrefix.
                                $paymentDTO->customerAccount,\json_encode([])), true);
        
                $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($paymentDTO->payments_provider_id);

                Queue::later(Carbon::now()->addSeconds((int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_SUBMIT_PAYMENT']), 
                                new InitiatePaymentJob($paymentDTO),'','high');
            }
        }
        
    }
    
}
