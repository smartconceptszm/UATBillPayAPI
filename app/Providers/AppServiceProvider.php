<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //Menu Home
         $this->app->singleton('ParentMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ParentMenu::class);
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
         $this->app->bind('CheckBalanceComplex', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\CheckBalanceComplex::class);
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

      //Billing Clients
			$this->app->singleton('BillingMock', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\BillingMock::class);
			});            
		//

		//Receipting Payment
			$this->app->singleton('MockReceipting', function () {
            return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\ReceiptPaymentMock::class);
			});            
		//

      //USSD Error Response Handlers
         $this->app->singleton('InvalidConfirmation', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidConfirmation::class);
         });
         $this->app->singleton('MaintenanceMode', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\MaintenanceMode::class);
         });
         $this->app->singleton('InvalidAccount', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAccount::class);
         });
         $this->app->singleton('InvalidAmount', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAmount::class);
         });
         $this->app->singleton('ClientBlocked', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\ClientBlocked::class);
         });
         $this->app->singleton('InvalidInput', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidInput::class);
         });
         $this->app->singleton('SystemError', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\SystemError::class);
         });
         $this->app->singleton('MoMoOffline', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\MoMoOffline::class);
         });
      //

      //MoMo Clients
			$this->app->singleton('ZAMTEL', function () {
            return $this->app->make(\App\Http\Services\External\MoMoClients\ZamtelKwacha::class);
				});
			$this->app->singleton('AIRTEL', function () {
               return $this->app->make(\App\Http\Services\External\MoMoClients\AirtelMoney::class);
				});
			$this->app->singleton('MTN', function () {
               return $this->app->make(\App\Http\Services\External\MoMoClients\MTNMoMo::class);
				});
         $this->app->singleton('MoMoMock', function () {
               return $this->app->make(\App\Http\Services\External\MoMoClients\MoMoMock::class);
				});
      //

      //SMS Clients
         $this->app->singleton('MTNDeliverySMS', function () {
            return $this->app->make(\App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS::class);
         });
         $this->app->singleton('MockSMSDelivery', function () {
            return $this->app->make(\App\Http\Services\External\SMSClients\MockSMSDelivery::class);
         });
			$this->app->singleton('ZAMTELSMS', function () {
            return $this->app->make(\App\Http\Services\External\SMSClients\ZamtelSMS::class);
				});
			$this->app->singleton('KANNEL', function () {
            return $this->app->make(\App\Http\Services\External\SMSClients\Kannel::class);
			});
      //

      //Scheduled Task Classes
         $this->app->bind(RetryFailedTrasactions::class, function () {
            return $this->app->make(\App\Http\ScheduledTasks\RetryFailedTrasactions::class);
         });
      //
      
   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }

}
