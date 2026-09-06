<?php

namespace App\Http\Services\PublicAPI;

interface IInitiateAPIPayment 
{
   public function handle(array $data);
}