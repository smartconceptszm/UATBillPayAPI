<?php

use App\Http\Services\External\BillingClients\Nkana\PurchaseEncryptor;
use Illuminate\Support\Str;
use Tests\TestCase;

class NkanaPurchaseEncryptorTest extends TestCase
{


   public function _testPurchaseEncryptor()
   { 


    //================================================================
    //sample

    $ecrptor = new PurchaseEncryptor();

    $transactionId = Str::random(16); //'#2017laisontech#'; //"912e46df7dd24e8e"; //
    $payment = '1280.50';
    
    $purchasestring = $ecrptor->generatePurchaseString($transactionId, $payment);

      $this->assertTrue($purchasestring == "95CC73B39265D1988120F7DBCF6E5C12");
      
   }

}