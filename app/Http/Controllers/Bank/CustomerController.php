<?php

namespace App\Http\Controllers\Bank;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Bank\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

   private $client;

	public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService,
      private Request $request)
	{
      $urlPrefix = $request->header('X-Client-ID');
      $client = $this->clientService->findOneBy(["urlPrefix" =>$urlPrefix]);
      $this->client = $client;
      $menu = $this->clientMenuService->findOneBy([   'client_id' => $client->id,
                                                      'paymentType'=>"POST-PAID",
                                                      'isPayment' => "YES",
                                                      'isDefault' => "YES",
                                                      'isActive' => "YES",
                                                   ]);
      $this->sclExternalServiceBinder->bindBillingClient($client->urlPrefix,$menu->id);
   }

   /**
    * Display the specified resource.
      */
   public function show(CustomerService $customerService, string $id)
   {

      $data = $customerService->getCustomer(["customerAccount"=>$id,
                                             "client_id"=>$this->client->id,
                                             "urlPrefix"=>$this->client->urlPrefix
                                          ]);
      return $this->successResponse($data, 200);

   }

}
