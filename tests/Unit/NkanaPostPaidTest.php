<?php

namespace Tests\Unit\Http\Services\External\BillingClients;

use Tests\TestCase;
use App\Http\Services\External\BillingClients\NkanaPostPaid;

class NkanaPostPaidTest extends TestCase
{
    /**
     * Test successful complaint submission.
     */
    public function testPostComplaintSuccess()
    {
        // Resolve NkanaPostPaid using Laravel's container
        $nkanaPostPaid = $this->app->make(NkanaPostPaid::class);

        // Input data
        $postParams = [
            'custkey' => '076258744',
            'complaintDescription' => 'Water is blue',
            'clientPhoneNumber' => '0972702707',
            'postPaid' => 'YES',
            'client_id'=>'39d62961-7303-11ee-b8ce-fec6e52a2330'
        ];

        // Call the method (returns JSON string)
        $jsonResponse = $nkanaPostPaid->postComplaint($postParams);

        // Decode the JSON response into an associative array
        $response = json_decode($jsonResponse, true);

        // Assert the response structure and values
        $this->assertArrayHasKey('statusCode', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('complaintNum', $response);

        // Assert success-specific values
        $this->assertEquals('OT001', $response['statusCode']);
        $this->assertEquals('Complaint was added', $response['message']);

        // Assert complaintNum is an array with a nested complaintNum key
        $this->assertIsArray($response['complaintNum']);
        $this->assertArrayHasKey('complaintNum', $response['complaintNum']);
        $this->assertNotEmpty($response['complaintNum']['complaintNum']);
    }

      public function testPostOtherPaymentsSuccess()
    {
        // Resolve NkanaPostPaid using Laravel's container
        $nkanaPostPaid = $this->app->make(NkanaPostPaid::class);

        // Input data
        $postParams = [
            'selectedId' => '2',
            'amount' => '1',
            'clientRefnumber' => 'Illegal Test2 ' . now()->timestamp,
            'client_id'=>'39d62961-7303-11ee-b8ce-fec6e52a2330'
        ];

        // Call the method (returns JSON string)
        $response = $nkanaPostPaid->postOtherPayment($postParams);


         $this->assertEquals('SUCCESS', $response['status']);
        $this->assertEquals('', $response['error']);
        $this->assertNotEmpty($response['receiptNumber']);
        $this->assertStringStartsWith('SC', $response['receiptNumber']);


    }


    /**
     * Test complaint submission with missing fields.
     */
    public function testPostComplaintMissingFields()
    {
        // Resolve NkanaPostPaid using Laravel's container
        $nkanaPostPaid = $this->app->make(NkanaPostPaid::class);

        // Input data with missing fields
        $postParams = [
            'custkey' => '076258744',
            // Missing complaintDescription and clientPhoneNumber
        ];

        // Call the method (returns JSON string)
        $jsonResponse = $nkanaPostPaid->postComplaint($postParams);

        // Decode the JSON response into an associative array
        $response = json_decode($jsonResponse, true);

        // Assert the response indicates failure
        $this->assertEquals('OT002', $response['statusCode']);
        $this->assertEquals('Complaint was not added', $response['message']);
        $this->assertNull($response['complaintNum']);
    }
}
