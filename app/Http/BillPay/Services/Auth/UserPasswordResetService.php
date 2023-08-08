<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\BillPay\Services\SMS\SMSService;
use App\Http\BillPay\Services\Auth\UserService;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\SMSTxDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Exception;

class UserPasswordResetService implements ICreateService, IUpdateService
{

   private $userService;
   private $smsService;
   private $dto;
   public function __construct(SMSService $smsService, 
      UserService $userService, SMSTxDTO $dto)
   {
      $this->userService = $userService;
      $this->smsService = $smsService;
      $this->dto = $dto;
   }

   public function create(array $data):object|null
   {

      try {
         $user = $this->userService->findOneBy($data);
         if($user){
               $resetPIN = Str::random(6);
               $this->dto = $this->dto->fromArray([
                  'mobileNumber' => $user->mobileNumber,
                  'client_id' => $user->client_id,
                  'message' => $resetPIN,
                  'type' => "NOTIFICATION",
               ]);
               $this->dto = $this->smsService->send($this->dto);
               if($this->dto->status == 'DELIVERED'){
                  Cache::put($user->username.'.'.$user->mobileNumber,$resetPIN, Carbon::now()->addMinutes(intval(\env('PASSWORD_RESET'))));
                  return (object)['description'=>'Password reset SMS Notification SENT!'];
               }else{
                  throw new Exception("Error sending Password reset key. Try again later");
               }
         }else{
               throw new Exception("No user found with the provided username");
         }
      } catch (\Throwable $e) {
         throw new Exception("Error at forgot password service: ".$e->getMessage());
      }
      
   }

   public function update(array $data, string $id):object|null
   {

      try {
         $dto = (object)$data;
         $user = $this->userService->findOneBy(['username'=>$dto->username]);
         $resetPIN = Cache::get( $user->username.".". $user->mobileNumber,'');
         if($resetPIN == $dto->resetPIN){
            return $this->userService->update(['password'=>$dto->password],$user->id);
         }else{
            throw new Exception("Invalid password reset PIN. Please try again!");
         }
      } catch (\Throwable $e) {
         throw new Exception("Error at reset password service: ".$e->getMessage());
      }

   }

}
