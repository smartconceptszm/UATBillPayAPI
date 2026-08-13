<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class NkanaPostPaid implements IBillingClient
{

   public function __construct(private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/ClientDetails/Customer_Details";

         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' => '/',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->get($fullURL, ["customerID"=> $params['customerAccount']]);

         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if($apiResponse['MsgStatusCode'] == 'ENQ001'){
               $response['customerAccount'] = $params['customerAccount'];
               $response['name'] =   $apiResponse['Cus_Details']['0']['INITIAL']."".$apiResponse['Cus_Details']['0']['SURNAME'];
               $response['address'] = $apiResponse['Cus_Details']['0']['UA_ADRESS1'];
               $response['composite'] = 'ORDINARY';
               $response['revenuePoint'] = "OTHER";
               $response['consumerTier'] = '';
               $response['consumerType'] = '';
               $response['mobileNumber'] =  $apiResponse['Cus_Details']['0']['CELL_TEL_NO'];
               $response['balance'] = $apiResponse['Cus_Details']['0']['Closing_Balance'];
            }else{
               if($apiResponse['MsgStatusCode'] == 'Enq005'){
                  //throw new Exception($apiResponse['statusNarration'], 1);
                  throw new Exception("Invalid Nkana POST-PAID Account Number", 1);
               }else{
                  throw new Exception($apiResponse['StatusNarration'], 2);
               }
            }
         } else {
            throw new Exception("status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw new Exception($e->getMessage(), 1);
         } else {
            throw new Exception("NKANA Remote Service responded with: " . $e->getMessage(), 2);
         }
      }

      return $response;
   }

   public function postPayment(Array $postParams): Array
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/Payment/ClientPayment";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' =>  '*/*',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->post($fullURL, $postParams);
         if ($apiResponse->status() == 201) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['Trax_Code'] == 'CPM-003'){
                  $response['status']="SUCCESS";
                  $response['receiptNumber']=$apiResponse['ClientPayment_gen']['cp_refNumber'];
               }else{
                  throw new Exception(' NKANA Billing Client error. Details: '.
                                       $apiResponse['Trax_Code'].' '.$apiResponse['ClientPayment_gen']['cpPaymentStatus'],1);
               }
         } else {
            if ($apiResponse->status() >= 400) {
               throw new Exception(' NKANA Billing Client error. Status code: '.$apiResponse->status(),1);
            } else {
               throw new Exception(" Status code: " . $apiResponse->status(), 2);
            }
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" NKANA Billing Client error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   public function postOtherPayment(array $postParams): array
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseComplaintsURL']."nwsc-api/v2/Transaction/ServiceConnection";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' =>  '*/*',
                                    'AuthenticationCode'=> 'Basic ' .$configs['AuthenticationCode'],
                                 ])
                              ->post($fullURL, $postParams);

         $body = $apiResponse->json();

         if (!is_array($body)) {
               throw new Exception(
                  ' NKANA Billing Client V2 error. Details: Invalid response (HTTP '
                  . $apiResponse->status() . '): ' . $apiResponse->body(),
                  1
               );
         }

         $statusCode = $body['statusCode'] ?? null;
         $message    = $body['message'] ?? 'No message returned';

         if ($apiResponse->status() == 200 && $statusCode == 'OT001') {
            $response['status'] = "SUCCESS";
            $response['receiptNumber'] = $body['clientPaymentgen']['refNumber'] ?? '';
         } else {
            throw new Exception(
               ' NKANA Billing Client V2 error. Details: '
               . ($statusCode ?? 'UNKNOWN') . ' ' . $message
               . ' (HTTP ' . $apiResponse->status() . ')',
               1
            );
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" NKANA Billing Client V2 error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }
   public function postComplaint(array $postParams): string
    {
        // Default response structure
        $response = [
            'statusCode' => 'OT002',
            'message' => 'Complaint was not added',
            'complaintNum' => null,
        ];

        try {
            // Validate required input fields
            if (
                !isset($postParams['custkey']) ||
                !isset($postParams['complaintDescription']) ||
                !isset($postParams['postPaid']) ||
                !isset($postParams['clientPhoneNumber'])
            ) {
                throw new \InvalidArgumentException('Missing required fields: custkey, complaintDescription, or clientPhoneNumber.');
            }

            // Resolve client-specific config (auth code, base URL, timeout)
            $configs = $this->getConfigs($postParams['client_id']);
            $fullURL =$configs['baseComplaintsURL'] . 'nwsc-api/v2/Transaction/ComplaintsRegistration';

            $apiResponse = Http::timeout($configs['timeout'])
                ->withHeaders([
                    'Accept' => '*/*',
                    'AuthenticationCode' => 'Basic ' .$configs['AuthenticationCode'],
                ])
                ->post($fullURL, [
                    'custkey' => $postParams['custkey'],
                    'complaintDescription' => $postParams['complaintDescription'],
                    'clientPhoneNumber' => $postParams['clientPhoneNumber'],
                    'postPaid' => $postParams['postPaid']
                ]);

            if ($apiResponse->status() == 200) {
                $apiResponse = $apiResponse->json();

                // NOTE: confirm actual success indicator + payload key names against the
                // Scalar docs for this endpoint — copied here from the ClientPayment shape.
                if ($apiResponse['statusCode'] == 'OT001') {
                    $response['statusCode'] = 'OT001';
                    $response['message'] = 'Complaint was added';
                    $response['complaintNum'] = [
                        'complaintNum' => $apiResponse['complaintNum']['complaintNum'],
                    ];
                } else {
                    throw new \Exception(
                        ' NKANA Billing Client error. Details: ' .
                        $apiResponse['Trax_Code'] . ' ' .
                        ($apiResponse['ComplaintsRegistration_gen']['status'] ?? ''),
                        1
                    );
                }
            } else {
                if ($apiResponse->status() >= 400) {
                    throw new \Exception(' NKANA Billing Client error. Status code: ' . $apiResponse->status(), 1);
                } else {
                    throw new \Exception(' Status code: ' . $apiResponse->status(), 2);
                }
            }
        } catch (\Throwable $e) {
            $response['statusCode'] = 'OT002';
            $response['message'] = 'Complaint was not added';
            $response['complaintNum'] = null;

            if ($e->getCode() == 1) {
                $response['error'] = $e->getMessage();
            } else {
                $response['error'] = ' NKANA Billing Client error. Details: ' . $e->getMessage();
            }
        }

        return json_encode($response, JSON_PRETTY_PRINT);
    }

   public function postComplaintMock(array $postParams): string
    {
        // Default response structure
        $response = [
            'statusCode' => 'OT002',
            'message'    => 'Complaint was not added',
            'complaintNum' => null,
        ];

        try {
            // Validate required input fields
            if (
                !isset($postParams['custkey']) ||
                !isset($postParams['complaintDescription']) ||
                !isset($postParams['clientPhoneNumber'])
            ) {
                throw new \InvalidArgumentException('Missing required fields: custkey, complaintDescription, or clientPhoneNumber.');
            }

            // Simulate processing (e.g., saving to DB, calling an API, etc.)
            // Generate a complaint number (e.g., 17501307)
            $complaintNum = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

            // Set success response
            $response = [
                'statusCode' => 'OT001',
                'message'    => 'Complaint was added',
                'complaintNum' => ['complaintNum' => $complaintNum],
            ];

        } catch (\Throwable $e) {
            // Error response: complaintNum is null
            $response = [
                'statusCode' => 'OT002',
                'message'    => 'Complaint was not added',
                'complaintNum' => null,
            ];
        }

        // Encode the response as JSON
        return json_encode($response, JSON_PRETTY_PRINT);
    }

   private function getConfigs(string $client_id)
   {

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['AuthenticationCode'] = $clientCredentials['AuthenticationCode'];
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL'];
      $configs['baseComplaintsURL'] = $clientCredentials['POSTPAID_BASE_COMPLAINT_URL'];
      $configs['timeout'] = $clientCredentials['POSTPAID_TIMEOUT'];
      return $configs;

   }


}
