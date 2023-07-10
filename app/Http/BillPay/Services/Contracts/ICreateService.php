<?php

namespace App\Http\BillPay\Services\Contracts;

interface ICreateService
{
   public function create(array $data):object|null;
}
