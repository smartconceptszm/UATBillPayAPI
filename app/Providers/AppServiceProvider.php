<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider implements DeferrableProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //Menu Home
         $this->app->singleton('Home', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\Home::class);
         });
      //

      //Menu PayBill
         $this->app->singleton('PayBill', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\PayBill::class);
         });
         //Menu Step Handlers
            $this->app->singleton('PayBill_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\PayBill\PayBill_Step_1::class);
            });
            $this->app->singleton('PayBill_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\PayBill\PayBill_Step_2::class);
            });
            $this->app->singleton('PayBill_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\PayBill\PayBill_Step_3::class);
            });
            $this->app->singleton('PayBill_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\PayBill\PayBill_Step_4::class);
            });
            $this->app->singleton('PayBill_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\PayBill\PayBill_Step_5::class);
            });
         //
      //

      //BuyUnits
         $this->app->singleton('BuyUnits', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\BuyUnits::class);
         });
         //Menu Step Handlers
            $this->app->singleton('BuyUnits_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\BuyUnits\BuyUnits_Step_1::class);
            });
            $this->app->singleton('BuyUnits_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\BuyUnits\BuyUnits_Step_2::class);
            });
            $this->app->singleton('BuyUnits_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\BuyUnits\BuyUnits_Step_3::class);
            });
            $this->app->singleton('BuyUnits_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\BuyUnits\BuyUnits_Step_4::class);
            });
            $this->app->singleton('BuyUnits_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\BuyUnits\BuyUnits_Step_5::class);
            });
         //
      //

      //Menu CheckBalance
         $this->app->singleton('CheckBalance', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\CheckBalance::class);
         });
         //Menu Step Handlers
            $this->app->singleton('CheckBalance_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_1::class);
            });
            $this->app->singleton('CheckBalance_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_2::class);
            });
            $this->app->singleton('CheckBalance_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_3::class);
            });
            $this->app->singleton('CheckBalance_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_4::class);
            });
         //
      //
      
      //Menu Complaints
         $this->app->singleton('FaultsComplaints', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\FaultsComplaints::class);
         });
         //Menu Step Handlers
            $this->app->singleton('FaultsComplaints_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_1::class);
            });
            $this->app->singleton('FaultsComplaints_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_2::class);
            });
            $this->app->singleton('FaultsComplaints_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_3::class);
            });
            $this->app->singleton('FaultsComplaints_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_4::class);
            });
            $this->app->singleton('FaultsComplaints_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_5::class);
            });
            $this->app->singleton('FaultsComplaints_Step_6', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_6::class);
            });
         //
      //

      //Menu UpdateDetails
         $this->app->singleton('UpdateDetails', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\UpdateDetails::class);
         });
         //Menu Handler Steps
            $this->app->singleton('UpdateDetails_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_1::class);
            });
            $this->app->singleton('UpdateDetails_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_2::class);
            });
            $this->app->singleton('UpdateDetails_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_3::class);
            });
            $this->app->singleton('UpdateDetails_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_4::class);
            });
            $this->app->singleton('UpdateDetails_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_5::class);
            });
         //
      //

		//Receipting Payment
			$this->app->singleton('MockReceipting', function () {
				return new \App\Http\Services\MoMo\BillingClientCallers\PostPaymentMock();
			});            
		//



      //USSD Error Response Handlers
         $this->app->singleton('InvalidConfirmation', function () {
               return new \App\Http\Services\USSD\ErrorResponses\InvalidConfirmation();
         });
         $this->app->singleton('MaintenanceMode', function () {
               return new \App\Http\Services\USSD\ErrorResponses\MaintenanceMode();
         });
         $this->app->singleton('InvalidAccount', function () {
               return new \App\Http\Services\USSD\ErrorResponses\InvalidAccount();
         });
         $this->app->singleton('InvalidAmount', function () {
               return new \App\Http\Services\USSD\ErrorResponses\InvalidAmount();
         });
         $this->app->singleton('ClientBlocked', function () {
               return new \App\Http\Services\USSD\ErrorResponses\ClientBlocked();
         });
         $this->app->singleton('InvalidInput', function () {
               return new \App\Http\Services\USSD\ErrorResponses\InvalidInput();
         });
         $this->app->singleton('SystemError', function () {
               return new \App\Http\Services\USSD\ErrorResponses\SystemError();
         });
         $this->app->singleton('MoMoOffline', function () {
               return new \App\Http\Services\USSD\ErrorResponses\MoMoOffline();
         });
         $this->app->singleton('NoError', function () {
               return new \App\Http\Services\USSD\ErrorResponses\NoError();
         });
      //

      //MoMo Clients
			$this->app->singleton('ZAMTEL', function () {
					return new \App\Http\Services\External\MoMoClients\ZamtelKwacha();
				});
			$this->app->singleton('AIRTEL', function () {
					return new \App\Http\Services\External\MoMoClients\AirtelMoney();
				});
			$this->app->singleton('MTN', function () {
					return new \App\Http\Services\External\MoMoClients\MTNMoMo();
				});
         $this->app->singleton('MoMoMock', function () {
					return new \App\Http\Services\External\MoMoClients\MoMoMock();
				});
      //

      //SMS Clients
         $this->app->singleton('MTNDeliverySMS', function () {
               return new \App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS();
         });
         $this->app->singleton('MockDeliverySMS', function () {
               return new \App\Http\Services\External\SMSClients\MockDeliverySMS();
         });
			$this->app->singleton('ZAMTELSMS', function () {
						return new \App\Http\Services\External\SMSClients\ZamtelSMS();
				});
			$this->app->singleton('KANNEL', function () {
					return new \App\Http\Services\External\SMSClients\Kannel();
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
         
         'PayBill',

			'CheckBalance','CheckBalance_Step_1','CheckBalance_Step_2',
         'CheckBalance_Step_3','CheckBalance_Step_4', 

         'FaultsComplaints',
         
         'UpdateDetails',
         
         'BuyUnits',


			'Cleanup',
         

         
         'Home',

			'MockReceipting',



         'InvalidConfirmation','MaintenanceMode','InvalidAccount',
         'InvalidAmount','ClientBlocked','InvalidInput',
         'SystemError','MoMoOffline','NoError',

         'MoMoMock','ZAMTEL','AIRTEL','MTN',

         'MTNDeliverySMS','MockDeliverySMS','ZAMTELSMS','KANNEL'

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
