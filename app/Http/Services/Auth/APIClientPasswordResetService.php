<?php

namespace App\Http\Services\Auth;

use App\Http\Services\Auth\APIClientService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\SendSMSesJob;
use Exception;

class APIClientPasswordResetService
{

   public function __construct(
      private APIClientService $apiClientService,
      private ClientService $clientService)
   {}

   public function create(array $data):string
   {

      try {
         $user = $this->apiClientService->findOneBy($data);
         if($user){
            $client = $this->apiClientService->findById($user->client_id);
            $resetPIN = Str::random(6);

            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            Cache::put($user->username.'.'.$user->mobileNumber,$resetPIN, Carbon::now()->addMinutes(intval($billpaySettings['PASSWORD_RESET'])));
            
            $client = $this->clientService->findOneBy(['urlPrefix'=>$billpaySettings['DEFAULT_URL_PREFIX']]);

            $smses =[[
                     'mobileNumber' => $user->mobileNumber,
                     'client_id' => $client->client_id,
                     'urlPrefix'=>$client->urlPrefix,
                     'message' => $resetPIN,
                     'type' => "NOTIFICATION",
                  ]];

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
         $user = $this->apiClientService->findOneBy(['username'=>$dto->username]);
         if($user){
            $resetPIN = Cache::get( $user->username.".". $user->mobileNumber,'');
            if($resetPIN == $dto->resetPIN){
               return $this->apiClientService->update(['password' => $dto->password],$user->id);
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
