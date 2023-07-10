<?php

namespace App\Http\BillPay\Services\Contracts;

interface IDeleteService
{
   public function delete(string $id):bool;
}
