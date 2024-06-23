<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\Services\Web\CRM\CustomerFieldUpdateDetailService;
use App\Http\Services\Web\MenuConfigs\CustomerFieldService;
use App\Http\Services\Web\CRM\CustomerFieldUpdateService;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateDetails_Local implements IUpdateDetailsClient
{

   public function __construct(
      private CustomerFieldUpdateDetailService $customerFieldUpdateDetailService,
      private CustomerFieldUpdateService $customerFieldUpdateService,
      private CustomerFieldService $customerFieldService)
   {}

   public function create(array $ticketData):string
   {

      try {
         $updatedFieldDetails = $ticketData['updates'];
         unset($ticketData['updates']);
         DB::beginTransaction();
         try {
               $updateTicket = $this->customerFieldUpdateService->create($ticketData);
               foreach ($updatedFieldDetails as $order => $value) {
                  $customerField = $this->customerFieldService->findOneBy([
                                          'client_id' => $ticketData['client_id'],
                                          'order' => $order
                                       ]);
                  $customerField = \is_null($customerField)?null: (object)$customerField->toArray();
                  $this->customerFieldUpdateDetailService->create([
                                    'customer_field_update_id' => $updateTicket->id,
                                    'customer_field_id' => $customerField->id,
                                    'value' => $value
                                 ]);
               }
               DB::commit();
         } catch (\Throwable $e) {
               DB::rollBack();
               throw new Exception($e->getMessage());
         }
      } catch (\Throwable $e) {
         throw new Exception('Error at  Cupdate details local. '.$e->getMessage());
      }
      return $updateTicket->caseNumber;                                           

   }

}