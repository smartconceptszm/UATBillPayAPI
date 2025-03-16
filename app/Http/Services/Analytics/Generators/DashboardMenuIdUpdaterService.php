<?php

namespace App\Http\Services\Analytics\Generators;

use App\Http\Services\Analytics\Dashboards\DashboardPaymentTypeTotalsService;
use App\Http\Services\Clients\ClientMenuService;

class DashboardMenuIdUpdaterService
{

   public function __construct(
      private DashboardPaymentTypeTotalsService $dashboardPaymentTypeTotalsService,
      private ClientMenuService $clientMenuService
) {}

   public function generate()
   {
      
      try {
         
         //Step 6 - Generate Payment Type Daily transactions totals
            $menuTotals = $this->dashboardPaymentTypeTotalsService->findAll(null);
            $ophannedRecords = [];
            foreach ($menuTotals as  $menuTotal) {
               $theMenu = $this->clientMenuService->findOneBy([
                                          'client_id' => $menuTotal->client_id,
                                          'prompt' => $menuTotal->paymentType,
                                       ]);
               if($theMenu){
                  $this->dashboardPaymentTypeTotalsService->update(['menu_id' => $theMenu->id],$menuTotal->id);
               }else{
                  $ophannedRecords[] = $menuTotal;
               }
               
            }

            // DashboardPaymentTypeTotals::upsert(
            //                               $menuTotalRecords,
            //                               ['client_id','menu_id','paymentType', 'dateOfTransaction'],
            //                               ['numberOfTransactions','totalAmount','year','month','day']
            //                            );
         //

      } catch (\Throwable $e) {
         return false;
      }

      return true;
      
   }

}
