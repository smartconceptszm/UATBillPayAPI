<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\IDeleteService;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserLogoutService implements IDeleteService
{

    /**
     * Inavlidate the JWT for login user.
     */
    public function delete(string $id):bool
    {

        try {
            Auth::logout();
            return true;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

    }
    

}
