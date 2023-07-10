<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IDeleteRepository
{
   public function delete(string $id):bool;
}