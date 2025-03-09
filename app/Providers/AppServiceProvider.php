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

      //DUMMY MENU
         $this->app->singleton('DummyMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\DummyMenu::class);
         });
      //

      //Next Page/Response Next
         $this->app->singleton('NextPage', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\NextPage::class);
         });
      //

      //PLACEHOLDER MENU
         $this->app->singleton('PlaceHolder', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\PlaceHolderMenu::class);
         });
      //

      //ResumePreviousSession MENU
         $this->app->singleton('ResumePreviousSession', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ResumePreviousSession::class);
         });
      //

      //Menu Make Payment
         $this->app->singleton('MakePayment', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\MakePayment::class);
         });
         $this->app->singleton('MakeOtherPayment', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\MakeOtherPayment::class);
         });
         //Menu Step Handlers
            $this->app->singleton('MakePayment_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_1::class);
            });
            $this->app->singleton('MakePayment_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_2::class);
            });
            $this->app->singleton('MakePayment_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_3::class);
            });
            $this->app->singleton('MakePayment_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_4::class);
            });
            $this->app->singleton('MakePayment_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_5::class);
            });
            $this->app->singleton('MakePayment_Step_6', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_6::class);
            });
         //
      //

      //Menu All Council Payments
			$this->app->singleton('CouncilPayment', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\CouncilPayment::class);
			});
			//Menu Step Handlers
				$this->app->singleton('CouncilPayment_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_1::class);
				});
				$this->app->singleton('CouncilPayment_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_2::class);
				});
				$this->app->singleton('CouncilPayment_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_3::class);
				});
				$this->app->singleton('CouncilPayment_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_4::class);
				});
				$this->app->singleton('CouncilPayment_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_5::class);
				});
            $this->app->singleton('CouncilPayment_Step_6', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_6::class);
				});
            $this->app->singleton('CouncilPayment_Step_7', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_7::class);
				});
			//

		// 

      //Shortcuts MENU
         $this->app->singleton('CheckPostPaidBalanceShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\CheckPostPaidBalanceShortcut::class);
         });
         $this->app->singleton('PayBillShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\MakePaymentShortcut::class);
         });
         $this->app->singleton('MakeCouncilPaymentShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\MakeCouncilPaymentShortcut::class);
         });
      //

      //Resume Payment Session
         $this->app->singleton('ResumePreviousSession', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ResumePreviousSession::class);
         });
         //Menu Step Handlers
            $this->app->singleton('ResumePreviousSession_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\ResumePreviousSession\ResumePreviousSession_Step_1::class);
            });
            $this->app->singleton('ResumePreviousSession_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\ResumePreviousSession\ResumePreviousSession_Step_2::class);
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
         $this->app->bind('FaultsComplaintsComplex', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\FaultsComplaintsComplex::class);
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

         //Client Callers
            $this->app->singleton('UpdateDetails_mock', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
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

      //Billing Clients
         $this->app->singleton('ReceiptingMock', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPaymentMock::class);
         });
			$this->app->singleton('MockBillingClient', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\BillingMock::class);
			});            
		//

		//Receipting Payment
			$this->app->singleton('MockReceipting', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPaymentMock::class);
			});            
		//

      //USSD Error Response Handlers
         $this->app->singleton('INVALIDCONFIRMATION', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidConfirmation::class);
         });
         $this->app->singleton('MAINTENANCEMODE', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\MaintenanceMode::class);
         });
         $this->app->singleton('INVALIDACCOUNT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAccount::class);
         });
         $this->app->singleton('INVALIDAMOUNT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAmount::class);
         });
         $this->app->singleton('CLIENTBLOCKED', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\ClientBlocked::class);
         });
         $this->app->singleton('INVALIDINPUT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidInput::class);
         });
         $this->app->singleton('INVALIDSURVEYRESPONSE', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidSurveyResponse::class);
         });
         $this->app->singleton('SYSTEMERROR', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\SystemError::class);
         });
         $this->app->singleton('WALLETNOTACTIVATED', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\WalletNotActivated::class);
         });
      //

      //Payments Provider Clients
			$this->app->singleton('ZAMTEL', function () {
            return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\ZamtelKwacha::class);
				});
			$this->app->singleton('AIRTEL', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\AirtelMoney::class);
				});
			$this->app->singleton('MTN', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\MTNMoMo::class);
				});
         $this->app->singleton('DPOPay', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\DPOPay::class);
				});
         $this->app->singleton('MockWallet', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\MockWallet::class);
				});
      //

      //SMS Clients
         $this->app->singleton('MockSMSDelivery', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MockSMSDelivery::class);
            });
         $this->app->singleton('MTNDeliverySMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS::class);
            });
         $this->app->singleton('MTNSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MTNSMS::class);
            });
			$this->app->singleton('ZAMTELSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\ZamtelSMS::class);
				});
      //

      //Aggregated
         $this->app->singleton('AggregatedParentMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\AggregatedParentMenu::class);
         });
         //Clients
            $this->app->singleton('AggregatedClient', function () {
               return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\AggregatedClient::class);
            });
         //
      //

      //Tenant ShortCodes
         $this->app->singleton('Tenants', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\Tenants::class);
         });
      //

      //Dashboard Generate Handlers
         $this->app->singleton('ConsumerTier', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardConsumerTierTotalsService::class);
         });
         $this->app->singleton('ConsumerType', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardConsumerTypeTotalsService::class);
         });
         $this->app->singleton('Hourly', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardHourlyTotalsService::class);
         });
         $this->app->singleton('PaymentProvider', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardPaymentProviderTotalsService::class);
         });
         $this->app->singleton('PaymentStatus', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardPaymentStatusTotalsService::class);
         });
         $this->app->singleton('PaymentType', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardPaymentTypeTotalsService::class);
         });
         $this->app->singleton('RevenueCollector', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardRevenueCollectorTotalsService::class);
         });
         $this->app->singleton('RevenuePoint', function () {
            return $this->app->make(\App\Http\Services\Analytics\Generators\DashboardRevenuePointTotalsService::class);
         });
      //

      //Dashboard View Handlers
         $this->app->singleton('ConsumerTierView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\ConsumerTierViewService::class);
         });
         $this->app->singleton('ConsumerTypeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\ConsumerTypeViewService::class);
         });
         $this->app->singleton('DailyByMonthView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\DailyByMonthViewService::class);
         });
         $this->app->singleton('DailyCumulativeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\DailyCommulativeViewService::class);
         });
         $this->app->singleton('HourlySalesView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\HourlySalesViewService::class);
         });
         $this->app->singleton('MonthlyOverYearView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\MonthlyOverYearViewService::class);
         });
         $this->app->singleton('PaymentProviderSummaryView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentProviderSummaryViewService::class);
         });
         $this->app->singleton('PaymentStatusView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentStatusViewService::class);
         });
         $this->app->singleton('PaymentTypeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentTypeViewService::class);
         });
         $this->app->singleton('RevenueCollectorView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenueCollectorViewService::class);
         });
         $this->app->singleton('RevenuePointView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenuePointViewService::class);
         });

         $this->app->singleton('RevenuePointUserView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenuePointUserViewService::class);
         });
         $this->app->singleton('PaymentProviderSummaryUserView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentProviderSummaryUserViewService::class);
         });
      //
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
