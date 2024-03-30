<?php

namespace App\Http\Services\External\BillingClients;

interface IBillingClient 
{

	public function getAccountDetails(array $enquiryParams): array;

	public function postPayment(Array $postParams): Array;

}