<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Queue;
use App\Jobs\InitiateMoMoPaymentJob;
use App\Http\DTOs\MoMoDTO;
use Illuminate\Support\Carbon;

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
                Queue::later(Carbon::now()->addSeconds((int)\env($momoDTO->mnoName.
                                    '_SUBMIT_PAYMENT')), new InitiateMoMoPaymentJob($momoDTO));
            }
        }
        
    }
    
}
