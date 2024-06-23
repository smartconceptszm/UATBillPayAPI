<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\InitiatePaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;

use Closure;

class FireMoMoRequestMiddleware
{

    public function __construct(
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
                                $paymentDTO->accountNumber,\json_encode([])), true);
                                
                Queue::later(Carbon::now()->addSeconds((int)\env($paymentDTO->walletHandler.
                                    '_SUBMIT_PAYMENT')), new InitiatePaymentJob($paymentDTO),'','high');
            }
        }
        
    }
    
}
