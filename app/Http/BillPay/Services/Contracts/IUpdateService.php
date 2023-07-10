<?php

namespace App\Http\BillPay\Services\Contracts;

interface IUpdateService
{
   public function update(array $data, string $id):object|null;
}
