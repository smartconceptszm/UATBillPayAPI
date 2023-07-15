<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Http\BillPay\Services\Auth\UserService;
use Illuminate\Support\Facades\Cache;
use Exception;

class UserResetPasswordService implements ICreateService
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService=$userService;
    }

    public function create(array $data):object|null
    {

        try {
            $dto = (object)$data;
            $user=$this->userService->findOneBy(['username'=>$dto->username]);
            $resetPIN=Cache::get( $user->username.".". $user->mobileNumber,'');
            if($resetPIN==$dto->resetPIN){
                return $this->userService->update(['password'=>$dto->password],$user->id);
            }else{
                throw new Exception("Invalid password reset PIN. Please try again!");
            }
        } catch (\Throwable $e) {
            throw new Exception("Error at reset password service: ".$e->getMessage());
        }

    }

}
