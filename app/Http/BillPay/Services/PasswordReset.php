<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\External\SMSClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class PasswordReset 
{

    private $smsClient;

    public function __construct()
    {
        $this->smsClient=new SMSClient();
    }
    
    public function generatePIN(String $username):array|null
    {

        $response = [
            'message'=>"Error generating password reset key",
            'status'=>500
        ];
        try {
            $query = DB::table('users as u')
                ->join('clients as c','c.id','=','u.client_id')
                ->select('u.id','u.username','u.mobileNumber','c.shortName')
                ->where([['u.username', '=', $username]])
                ->get();
            if ($query->count() > 0) {
                $user=$query[0];
                $resetPIN=Str::random(6);
                $smsParams=[];
                $smsParams['message']=$resetPIN;
                $smsParams['mobileNumber']=$user->mobileNumber;
                $smsParams['clientShortName']=$user->shortName;
                $smsSent =$this->smsClient->sendSMS($smsParams);
                if($smsSent){
                    Cache::put("user".$user->mobileNumber,$resetPIN, Carbon::now()->addMinutes(intval(\env('PASSWORD_RESET'))));
                    Log::info('Password reset SMS Notification SENT to '.$user->mobileNumber);
                    $response['message']='Password reset SMS Notification SENT!';
                    $response['status']=200;
                }else{
                    $response['message']="Error sending Password reset key. Try again later";
                    $response['status']=500;
                }
            }else{
                $response['message']="User not found!";
                $response['status']=404;
            }

        } catch (\Throwable $e) {
            $response['message']=$e->getMessage();
            $response['status']=500;
            Log::error('Error generating password reset key. DETAILS: '.$e->getMessage());
        }
        return $response;

    }

    public function resetPassword(Array $resetParams): array|null
    {

        $response = [
            'message'=>"Invalid password reset key. Please try again!",
            'id'=>'',
            'status'=>401
        ];

        try {
            $query = DB::table('users')
                ->select('id','username','mobileNumber')
                ->where([['username', '=', $resetParams['username']]])
                ->get();
            if ($query->count() > 0) {
                $user=$query[0];
                $resetPIN=Cache::get("user".$user->mobileNumber,'');
                if($resetPIN==$resetParams['resetPIN']){
                    $response['message']="Sussess";
                    $response['id']=$user->id;
                    $response['status']=200;
                }else{
                    $response['message']="Invalid password reset PIN. Please try again!";
                    $response['status']=401;
                }
            }else{
                $response['message']="User not found!";
                $response['status']=401;
            }
        } catch (\Throwable $e) {
            $response['message']=$e->getMessage();
            $response['status']=500;
            Log::error('Error resetting password. DETAILS: '.$e->getMessage());
        }
        return $response;
    }

}
