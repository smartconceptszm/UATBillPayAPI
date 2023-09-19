<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\Services\CRM\CustomerFieldUpdateDetailService;
use App\Http\Services\MenuConfigs\CustomerFieldService;
use App\Http\Services\CRM\CustomerFieldUpdateService;
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
                  $fieldDetail = $this->customerFieldUpdateDetailService->create([
                        'customer_field_update_id' => $updateTicket->id,
                        'customer_field_id' => $customerField->id,
                        'value' => $value
                     ]);
               }
               DB::commit();
         } catch (Exception $e) {
               DB::rollBack();
               throw new Exception($e->getMessage());
         }
      } catch (Exception $e) {
         throw new Exception('Error at  Cupdate details local. '.$e->getMessage());
      }
      return $updateTicket->caseNumber;                                           

   }

}