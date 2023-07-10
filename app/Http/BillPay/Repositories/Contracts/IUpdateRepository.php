<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IUpdateRepository
{
   public function update(array $data, string $id):object|null;
}