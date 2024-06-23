<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwascoServiceProvider extends ServiceProvider
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

		//Survey Entry Handlers
			$this->app->singleton('Survey_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//

		//SMS Clients
			$this->app->singleton('SWASCOSMS', function () {
				return $this->app->make(\App\Http\Services\External\SMSClients\SwascoSMS::class);
			});
		//

			$this->app->singleton('swascoPostPaidEnquiry', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\SwascoEnquiry::class);
         });

			$this->app->singleton('ReceiptPostPaidSwasco', function () {
				return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPostPaidSwasco::class);
			});

			$this->app->singleton('ReceiptReconnectionSwasco', function () {
				return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptReconnectionSwasco::class);
			});
			
			$this->app->singleton('ReceiptVacuumTankerSwasco', function () {
				return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptVacuumTankerSwasco::class);
			});
		//

		//Complaint Handlers
			$this->app->singleton('Complaint_swasco', function () {
					return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Swasco::class);
			});
		//
  
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
