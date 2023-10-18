<?php

namespace App\Providers;

//Laravel Dependancies
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SwascoServiceProvider extends ServiceProvider implements DeferrableProvider
{

	/**
	 * Register services.
	 */
	public function register(): void
	{

		//Menu Update Mobile Number 
			$this->app->singleton('SwascoUpdateMobile', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\SwascoUpdateMobile::class);
			});
			//Menu Step Handlers
				$this->app->singleton('SwascoUpdateMobile_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\SwascoUpdateMobile\SwascoUpdateMobile_Step_1::class);
				});
				$this->app->singleton('SwascoUpdateMobile_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\SwascoUpdateMobile\SwascoUpdateMobile_Step_2::class);
				});
				$this->app->singleton('SwascoUpdateMobile_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\SwascoUpdateMobile\SwascoUpdateMobile_Step_3::class);
				});
				$this->app->singleton('SwascoUpdateMobile_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\SwascoUpdateMobile\SwascoUpdateMobile_Step_4::class);
				});
			//
		//

		//Menu Pay Reconnection Fees
			$this->app->singleton('ReconnectionFeesSwasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\ReconnectionFeesSwasco::class);
			});
			//Menu Step Handlers
				$this->app->singleton('ReconnectionFeesSwasco_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\ReconnectionFeesSwasco\ReconnectionFeesSwasco_Step_2::class);
				});
				$this->app->singleton('ReconnectionFeesSwasco_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\ReconnectionFeesSwasco\ReconnectionFeesSwasco_Step_3::class);
				});
				$this->app->singleton('ReconnectionFeesSwasco_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\ReconnectionFeesSwasco\ReconnectionFeesSwasco_Step_4::class);
				});
				$this->app->singleton('ReconnectionFeesSwasco_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\ReconnectionFeesSwasco\ReconnectionFeesSwasco_Step_5::class);
				});
				$this->app->singleton('ReconnectionFeesSwasco_Step_6', function () {
					return $this->app->make(\App\Http\Services\USSD\ReconnectionFeesSwasco\ReconnectionFeesSwasco_Step_6::class);
				});
			//
		//	

		//Menu Pay for Vacuum tanker
			$this->app->singleton('VacuumTankerSwasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\VacuumTankerSwasco::class);
			});
			//Menu Step Handlers
				$this->app->singleton('VacuumTankerSwasco_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\VacuumTankerSwasco\VacuumTankerSwasco_Step_2::class);
				});
				$this->app->singleton('VacuumTankerSwasco_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\VacuumTankerSwasco\VacuumTankerSwasco_Step_3::class);
				});
				$this->app->singleton('VacuumTankerSwasco_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\VacuumTankerSwasco\VacuumTankerSwasco_Step_4::class);
				});
				$this->app->singleton('VacuumTankerSwasco_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\VacuumTankerSwasco\VacuumTankerSwasco_Step_5::class);
				});
				$this->app->singleton('VacuumTankerSwasco_Step_6', function () {
					return $this->app->make(\App\Http\Services\USSD\VacuumTankerSwasco\VacuumTankerSwasco_Step_6::class);
				});
			//
		//	

		//USSD Survey
			$this->app->singleton('Survey', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\Survey::class);
			});
			//Menu Step Handlers
				$this->app->singleton('Survey_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_1::class);
				});
				$this->app->singleton('Survey_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_2::class);
				});
				$this->app->singleton('Survey_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_3::class);
				});
				$this->app->singleton('Survey_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_5::class);
				});
			//
		//
		//Survey Entry Handlers
			$this->app->singleton('Survey_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//

		//SMS Clients
			$this->app->singleton('SWASCOSMS', function () {
				return $this->app->make( \App\Http\Services\External\SMSClients\SwascoSMS::class,[
											\env('SWASCO_SMS_BASE_URL'),
											env('SWASCO_SMS_APIKEY'),
											env('SWASCO_SMS_SENDER_ID'),
										]);
			});
		//

		//Billing Clients			
			$this->app->singleton('swasco', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\Swasco::class,[
									'swascoReceiptingTimeout'=>\intval(\env('SWASCO_RECEIPTING_TIMEOUT')),
									'swascoTimeout' => \intval(\env('SWASCO_REMOTE_TIMEOUT')),
									'baseURL' => \env('SWASCO_BASE_URL')
								]);
			});

			$this->app->singleton('ReceiptPaymentSwasco', function () {
				return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\ReceiptPaymentSwasco::class);
			});

			$this->app->singleton('ReceiptReconnectionFeesSwasco', function () {
				return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\ReceiptReconnectionFeesSwasco::class);
			});
			$this->app->singleton('ReceiptVacuumTankerSwasco', function () {
				return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\ReceiptVacuumTankerSwasco::class);
			});
		//

		//Complaint Handlers
			$this->app->singleton('Complaint_swasco', function () {
					return new \App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Swasco(
									$this->app->make(\App\Http\Services\External\BillingClients\Swasco::class,[                    
											'swascoReceiptingTimeout'=>\intval(\env('SWASCO_RECEIPTING_TIMEOUT')),
											'swascoTimeout' => \intval(\env('SWASCO_REMOTE_TIMEOUT')),
											'baseURL' => \env('SWASCO_BASE_URL')
										])
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

			'SwascoUpdateMobile','SwascoUpdateMobile_Step_1','SwascoUpdateMobile_Step_2',
			'SwascoUpdateMobile_Step_3','SwascoUpdateMobile_Step_4',

			'ReconnectionFeesSwasco','ReconnectionFeesSwasco_Step_2','ReconnectionFeesSwasco_Step_3',
			'ReconnectionFeesSwasco_Step_4','ReconnectionFeesSwasco_Step_5',
			'ReconnectionFeesSwasco_Step_6',

			'VacuumTankerSwasco','VacuumTankerSwasco_Step_2','VacuumTankerSwasco_Step_3',
			'VacuumTankerSwasco_Step_4','VacuumTankerSwasco_Step_5',
			'VacuumTankerSwasco_Step_6',

			'Survey','Survey_Step_1','Survey_Step_2','Survey_Step_3','Survey_Step_5',
			'Survey_swasco',

			'SWASCOSMS',
			
			'Complaint_swasco',
			
			'swasco','ReceiptPaymentSwasco','ReceiptReconnectionFeesSwasco','ReceiptVacuumTankerSwasco',
			
      ];

   }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
