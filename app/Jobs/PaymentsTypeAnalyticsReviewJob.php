<?php

namespace App\Jobs;

use App\Http\Services\Analytics\Dashboards\DashboardPaymentTypeTotalsService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class PaymentsTypeAnalyticsReviewJob extends BaseJob
{

   public $timeout = 180;

   public function handle(DashboardPaymentTypeTotalsService $dashboardPaymentTypeTotalsService,
                                                                  ClientMenuService $clientMenuService)
   {

      $ophannedRecords = [];
      $menuTotals = $dashboardPaymentTypeTotalsService->findAll(null);
      foreach ($menuTotals as  $menuTotal) {
         $theMenu = $clientMenuService->findOneBy([
                                    'client_id' => $menuTotal->client_id,
                                    'prompt' => $menuTotal->paymentType,
                                 ]);
         if($theMenu){
            $dashboardPaymentTypeTotalsService->update(['menu_id' => $theMenu->id],$menuTotal->id);
         }else{
            $ophannedRecords[] = $menuTotal;
         }
      }
      Log::info('Payments Type Analytics totals updated with Menu Ids. Ophaned records :'.count($ophannedRecords));

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}