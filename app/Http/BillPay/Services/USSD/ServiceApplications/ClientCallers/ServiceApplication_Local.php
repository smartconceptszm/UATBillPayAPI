<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers;

use App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\BillPay\Services\CRM\ServiceApplicationDetailService;
use App\Http\BillPay\Services\CRM\ServiceApplicationService;
use Exception;

class ServiceApplication_Local implements IServiceApplicationClient
{

   private $serviceAppDetailService;
   private $serviceTypeDetails;
   private $serviceAppService;
   public function __construct(ServiceApplicationDetailService $serviceAppDetailService,
      ServiceTypeDetailService $serviceTypeDetails,
      ServiceApplicationService $serviceAppService)
   {
      $this->serviceAppDetailService = $serviceAppDetailService;
      $this->$serviceAppService = $serviceAppService;
      $this->serviceTypeDetails = $serviceTypeDetails;
   }

   public function create(array $serviceApplicationData):string
   {

      try {
         DB::beginTransaction();
         try {
            $serviceTicket = $this->serviceAppService->create([
                                    'service_type_id' => $serviceApplicationData['service_type_id'],
                                    'accountNumber' => $serviceApplicationData['accountNumber'],
                                    'mobileNumber' => $serviceApplicationData['mobileNumber'],
                                    'client_id' => $serviceApplicationData['client_id'],
                                    'status' => 'SUBMITTED',
                                 ]);
            foreach ($serviceApplicationData['responses'] as $order => $value) {
               $applicationQuestion = $this->serviceTypeDetails->findOneBy([
                                       'service_type_id' => $serviceApplicationData['service_type_id'],
                                       'order' => $order
                                    ]);
               $serviceAppDetail = $this->serviceAppDetailService->create([
                                       'service_application_id' => $serviceTicket->id,
                                       'service_type_detail_id' => $applicationQuestion->id,
                                       'value' => $value
                                    ]);
            }
            DB::commit();
         } catch (\Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
         }
      } catch (\Exception $e) {
         throw new Exception('Error at  service application. '.$e->getMessage());
      }
      return $serviceTicket->caseNumber;                                             

   }

}