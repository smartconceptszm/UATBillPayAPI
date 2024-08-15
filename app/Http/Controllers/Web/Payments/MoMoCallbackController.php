<?php

namespace App\Http\Controllers\Web\Payments;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MoMoCallbackController extends Controller
{

    private $params=[];


    public function airtel(Request $request)
    {

        try {

            $callbackParams = $request->all();
            Log::info("Airtel Callback executed: ".json_encode($callbackParams)."\n".
                            "On path: ".$request->fullUrl()
                        );

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

