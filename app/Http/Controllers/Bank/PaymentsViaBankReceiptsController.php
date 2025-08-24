<?php

namespace App\Http\Controllers\Bank;

use App\Http\Services\Bank\PaymentsViaBankReceiptingService;
use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsViaBankReceiptsController extends Controller
{

   private $validationRules = [
                  'bankAccountNumber' => 'required|string',
                  'bankTransactionId' => 'required|string',
                  'customerAccount' => 'required|string',
                  'paymentAmount' => 'required|string',
                  'utility' => 'required|string',
                  'service' => 'required|string',
               ];
   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService,
      private Request $request)
   {
      $urlPrefix = $request->input('utility');
      $service  = $request->input('service');
      $client = $this->clientService->findOneBy(["urlPrefix" =>$urlPrefix]);
      $menu = $this->clientMenuService->findOneBy([   'client_id' => $client->id,
                                                      'paymentType'=>$service,
                                                      'isPayment' => "YES",
                                                      'isDefault' => "YES",
                                                      'isActive' => "YES",
                                                   ]);
      $this->sclExternalServiceBinder->bindBillingClient($client->urlPrefix,$menu->id);
   }

   public function store(Request $request, PaymentsViaBankReceiptingService $paymentsViaBankReceiptingService)
   {

      //validate incoming request 
      $params  = $this->validate($request, $this->validationRules);
      $params['ppTransactionId'] = $params['bankTransactionId'];
      $params['transactionId'] = $params['bankTransactionId'];
      $params['walletNumber'] = $params['bankAccountNumber'];
      $params['receiptAmount'] = $params['paymentAmount'];
      $params['urlPrefix'] = $params['utility'];
      // $data = ["receipt" => "Payment successful, receipt number 00012"];
      $data = $paymentsViaBankReceiptingService->handle($params);
      return $this->successResponse($data, 200);

   }

   
}
