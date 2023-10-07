<?php

namespace App\Providers;

//Service Applications Responses Handlers
use App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplication_Local;

//Faults/Complaints Handlers
use App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local;

//Customer Updates Handlers
use App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local;

//Billing Clients

//USSD Menu Services
use App\Http\Services\USSD\Menus\ServiceApplications;

//Laravel Dependancies
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ChambeshiServiceProvider extends ServiceProvider implements DeferrableProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //USSD Menu Option Handlers
         $this->app->singleton('ServiceApplications', function () {
            return new ServiceApplications();
         });
      //

      //Complaint Handlers
         $this->app->singleton('Complaint_chambeshi', function () {
               return new Complaint_Local( 
                  new \App\Http\Services\CRM\ComplaintService(
                     new \App\Models\Complaint()
                  )
               );
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_chambeshi', function () {
            return new UpdateDetails_Local(
                        new \App\Http\Services\CRM\CustomerFieldUpdateDetailService(                     
                           new \App\Models\CustomerFieldUpdateDetail()
                        ),
                  new \App\Http\Services\CRM\CustomerFieldUpdateService(
                           new \App\Models\CustomerFieldUpdate()
                        ),
                  new \App\Http\Services\MenuConfigs\CustomerFieldService(
                           new \App\Models\CustomerField()
                        )
               );
         });
      //

      //Service Application Handlers
         $this->app->singleton('ServiceApplications_chambeshi', function () {
               return new ServiceApplication_Local(
                     new \App\Http\Services\CRM\ServiceApplicationDetailService(
                        new \App\Models\ServiceApplicationDetail()
                     ),
                     new \App\Http\Services\MenuConfigs\ServiceTypeDetailService(
                        new \App\Models\ServiceTypeDetail()
                     ),
                     new \App\Http\Services\CRM\ServiceApplicationService(
                           new \App\Models\ServiceApplication()
                        )
                     );
            });
      //
      
   }

   /**
   * Get the services provided by the provider.
   *
   * @return array
   */
   public function provides()
   {

      return [
         'ServiceApplications','Complaint_chambeshi','Updates_chambeshi',
         'ServiceApplications_chambeshi'
      ];

   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }

}
