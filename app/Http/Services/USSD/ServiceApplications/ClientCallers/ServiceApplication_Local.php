<?php

namespace App\Http\Services\USSD\ServiceApplications\ClientCallers;

use App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use App\Http\Services\Web\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\Web\CRM\ServiceApplicationDetailService;
use App\Http\Services\Web\CRM\ServiceApplicationService;
use Illuminate\Support\Facades\DB;
use Exception;

class ServiceApplication_Local implements IServiceApplicationClient
{

   public function __construct(
      private ServiceApplicationDetailService $serviceAppDetailService,
      private ServiceTypeDetailService $serviceTypeDetails,
      private ServiceApplicationService $serviceAppService)
   {}

   public function create(array $serviceApplicationData):string
   {

      try {
         DB::beginTransaction();
         try {
            $serviceTicket = $this->serviceAppService->create([
                                    'service_type_id' => $serviceApplicationData['service_type_id'],
                                    'customerAccount' => $serviceApplicationData['customerAccount'],
                                    'mobileNumber' => $serviceApplicationData['mobileNumber'],
                                    'client_id' => $serviceApplicationData['client_id'],
                                    'status' => 'SUBMITTED',
                                 ]);
            foreach ($serviceApplicationData['responses'] as $order => $value) {
               $applicationQuestion = $this->serviceTypeDetails->findOneBy([
                                       'service_type_id' => $serviceApplicationData['service_type_id'],
                                       'order' => $order
                                    ]);
               $this->serviceAppDetailService->create([
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
      } catch (\Throwable $e) {
         throw new Exception('Error at  service application. '.$e->getMessage());
      }
      return $serviceTicket->caseNumber;                                             

   }

}