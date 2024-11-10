<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\MoMoCallbackService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MoMoCallbackController extends Controller
{

	public function __construct(
        private MoMoCallbackService $moMoCallbackService)
    {}

    public function airtel(Request $request)
    {

        try {

            $callbackParams = $request->all();
            $callbackParams = $callbackParams['transaction'];
            $this->moMoCallbackService->handleAirtel($callbackParams);
        } catch (\Throwable $e) {
            Log::error('Error processing Call Back Transaction: '.$e->getMessage());
        }
        
        return "Success"; 
                     
    }

    public function mtn(Request $request)
    {
        try {

            $callbackParams = $request->all();



            Log::info("MTN Callback executed: ".json_encode($callbackParams)."\n".
                            "On path: ".$request->fullUrl()
                        );
        
        } catch (\Throwable $e) {
            Log::error('Error processing Call Back Transaction: '.$e->getMessage());
        }
        return "Success";                 
    }

    public function zamtel(Request $request)
    {
        return "Success";       
    }

}

