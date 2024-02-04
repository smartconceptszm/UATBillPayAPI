<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\InitiateMoMoPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;

use Closure;

class FireMoMoRequestMiddleware
{

    private $momoDTO;
    public function __construct(MoMoDTO $momoDTO)
    {
        $this->momoDTO = $momoDTO;
    }
    
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

                $momoDTO =  $this->momoDTO->fromUssdData($ussdParams);
                $momoDTO->customer = \json_decode(Cache::get($momoDTO->urlPrefix.
                                $momoDTO->accountNumber,\json_encode([])), true);
                                
                Queue::later(Carbon::now()->addSeconds((int)\env($momoDTO->mnoName.
                                    '_SUBMIT_PAYMENT')), new InitiateMoMoPaymentJob($momoDTO));
            }
        }
        
    }
    
}
