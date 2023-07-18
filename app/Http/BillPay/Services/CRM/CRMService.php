<?php

namespace App\Http\BillPay\Services\CRM;

use App\Http\BillPay\Services\CRM\CustomerFieldUpdateDetailService;
use App\Http\BillPay\Services\MenuConfigs\CustomerFieldService;
use App\Http\BillPay\Services\CRM\CustomerFieldUpdateService;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Facades\DB;
use Exception;

class CRMService
{

   private $customerFieldUpdateDetailService;
   private $customerFieldUpdateService;
   private $customerFieldService;
   public function __construct(CustomerFieldUpdateDetailService $customerFieldUpdateDetailService,
      CustomerFieldUpdateService $customerFieldUpdateService,
      CustomerFieldService $customerFieldService)
   {
      $this->customerFieldUpdateDetailService = $customerFieldUpdateDetailService;
      $this->customerFieldUpdateService = $customerFieldUpdateService;
      $this->customerFieldService = $customerFieldService;
   }

   public function updateDetailsTicket(array $ticketData):string
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
         } catch (\Throwable $e) {
               DB::rollBack();
               throw new Exception($e->getMessage());
         }
      } catch (\Exception $e) {
         throw new Exception('Error at  CRMService@updateDetailTicket. '.$e->getMessage());
      }
      return $updateTicket->caseNumber;

   }

}
