<?php

namespace App\Http\Services\External\BillingClients;

interface IBillingClient 
{

	public function getAccountDetails(string $accountNumber): array;

	public function postPayment(Array $postParams): Array;

}