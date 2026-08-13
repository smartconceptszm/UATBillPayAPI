<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Clients\ClientService;
use App\Http\Services\Auth\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\SendSMSesJob;
use Exception;

class UserPasswordResetService
{

   public function __construct(
      private ClientService $clientService,
      private UserService $userService)
   {}

   public function create(array $data):string
   {

      try {
         $user = $this->userService->findOneBy($data);
         if($user){
            $client = $this->clientService->findById($user->client_id);
            $resetPIN = Str::random(6);
            $smses =[[
                     'mobileNumber' => $user->mobileNumber,
                     'client_id' => $user->client_id,
                     'urlPrefix'=>$client->urlPrefix,
                     'message' => $resetPIN,
                     'type' => "NOTIFICATION",
                  ]];
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            Cache::put($user->username.'.'.$user->mobileNumber,$resetPIN, Carbon::now()->addMinutes(intval($billpaySettings['PASSWORD_RESET'])));

            SendSMSesJob::dispatch($smses)
                           ->delay(Carbon::now()->addSeconds(1))
                           ->onQueue('high');

         }else{
            throw new Exception("Invalid username");
         }
      } catch (\Throwable $e) {
         throw new Exception("Error at forgot password service: ".$e->getMessage());
      }
      return 'Password reset SMS Notification SENT!';
      
   }

   public function update(array $data, string $id):object|null
   {

      try {
         $dto = (object)$data;
         $user = $this->userService->findOneBy(['username'=>$dto->username]);
         if($user){
            $resetPIN = Cache::get( $user->username.".". $user->mobileNumber,'');
            if($resetPIN == $dto->resetPIN){
               return $this->userService->update(['password' => $dto->password],$user->id);
            }else{
               throw new Exception("Invalid password reset PIN. Please try again!");
            }
         }else{
            throw new Exception("Invalid username");
         }
      } catch (\Throwable $e) {
         throw new Exception("Error at reset password service: ".$e->getMessage());
      }

   }

}
