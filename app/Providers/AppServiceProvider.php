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
         $this->app->bind('ParentMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ParentMenu::class);
         });
      //

      //DUMMY MENU
         $this->app->bind('DummyMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\DummyMenu::class);
         });
      //

      //Next Page/Response Next
         $this->app->bind('NextPage', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\NextPage::class);
         });
      //

      //PLACEHOLDER MENU
         $this->app->bind('PlaceHolder', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\PlaceHolderMenu::class);
         });
      //

      //ResumePreviousSession MENU
         $this->app->bind('ResumePreviousSession', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ResumePreviousSession::class);
         });
      //

      //Menu Make Payment
         $this->app->bind('MakePayment', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\MakePayment::class);
         });
         $this->app->bind('MakeOtherPayment', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\MakeOtherPayment::class);
         });
         //Menu Step Handlers
            $this->app->bind('MakePayment_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_1::class);
            });
            $this->app->bind('MakePayment_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_2::class);
            });
            $this->app->bind('MakePayment_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_3::class);
            });
            $this->app->bind('MakePayment_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_4::class);
            });
            $this->app->bind('MakePayment_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_5::class);
            });
            $this->app->bind('MakePayment_Step_6', function () {
               return $this->app->make(\App\Http\Services\USSD\MakePayment\MakePayment_Step_6::class);
            });
         //
      //

      //Menu All Council Payments
			$this->app->bind('CouncilPayment', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\CouncilPayment::class);
			});
			//Menu Step Handlers
				$this->app->bind('CouncilPayment_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_1::class);
				});
				$this->app->bind('CouncilPayment_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_2::class);
				});
				$this->app->bind('CouncilPayment_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_3::class);
				});
				$this->app->bind('CouncilPayment_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_4::class);
				});
				$this->app->bind('CouncilPayment_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_5::class);
				});
            $this->app->bind('CouncilPayment_Step_6', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_6::class);
				});
            $this->app->bind('CouncilPayment_Step_7', function () {
					return $this->app->make(\App\Http\Services\USSD\CouncilPayment\CouncilPayment_Step_7::class);
				});
			//

		// 

      //Menu All Council Payment History
         $this->app->bind('CouncilPaymentHistory', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\CouncilPaymentHistory::class);
         });
         //Menu Step Handlers
            $this->app->bind('CouncilPaymentHistory_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\CouncilPaymentHistory\CouncilPaymentHistory_Step_1::class);
            });
            $this->app->bind('CouncilPaymentHistory_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\CouncilPaymentHistory\CouncilPaymentHistory_Step_2::class);
            });
            $this->app->bind('CouncilPaymentHistory_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\CouncilPaymentHistory\CouncilPaymentHistory_Step_3::class);
            });
         //
      // 

      //Shortcuts MENU
         $this->app->bind('CheckPostPaidBalanceShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\CheckPostPaidBalanceShortcut::class);
         });
         $this->app->bind('PayBillShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\MakePaymentShortcut::class);
         });
         $this->app->bind('MakeCouncilPaymentShortcut', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\MakeCouncilPaymentShortcut::class);
         });
      //

      //Resume Payment Session
         $this->app->bind('ResumePreviousSession', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ResumePreviousSession::class);
         });
         //Menu Step Handlers
            $this->app->bind('ResumePreviousSession_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\ResumePreviousSession\ResumePreviousSession_Step_1::class);
            });
            $this->app->bind('ResumePreviousSession_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\ResumePreviousSession\ResumePreviousSession_Step_2::class);
            });
         //
      //

      //Menu CheckBalance
         $this->app->bind('CheckBalance', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\CheckBalance::class);
         });
         $this->app->bind('CheckBalanceComplex', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\CheckBalanceComplex::class);
         });
         //Menu Step Handlers
            $this->app->bind('CheckBalance_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_1::class);
            });
            $this->app->bind('CheckBalance_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_2::class);
            });
            $this->app->bind('CheckBalance_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_3::class);
            });
            $this->app->bind('CheckBalance_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\CheckBalance\CheckBalance_Step_4::class);
            });
         //
      //

      //Menu GetTokens
         $this->app->bind('GetTokens', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\GetTokens::class);
         });
         $this->app->bind('GetTokensComplex', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\GetTokensComplex::class);
         });
         //Menu Step Handlers
            $this->app->bind('GetTokens_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\GetTokens\GetTokens_Step_1::class);
            });
            $this->app->bind('GetTokens_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\GetTokens\GetTokens_Step_2::class);
            });
         //
      //
      
      //Menu Complaints
         $this->app->bind('FaultsComplaints', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\FaultsComplaints::class);
         });
         $this->app->bind('FaultsComplaintsComplex', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\FaultsComplaintsComplex::class);
         });
         //Menu Step Handlers
            $this->app->bind('FaultsComplaints_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_1::class);
            });
            $this->app->bind('FaultsComplaints_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_2::class);
            });
            $this->app->bind('FaultsComplaints_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_3::class);
            });
            $this->app->bind('FaultsComplaints_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_4::class);
            });
            $this->app->bind('FaultsComplaints_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_5::class);
            });
            $this->app->bind('FaultsComplaints_Step_6', function () {
               return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_Step_6::class);
            });
         //
      //

      //Menu UpdateDetails
         $this->app->bind('UpdateDetails', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\UpdateDetails::class);
         });
         //Menu Handler Steps
            $this->app->bind('UpdateDetails_Step_1', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_1::class);
            });
            $this->app->bind('UpdateDetails_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_2::class);
            });
            $this->app->bind('UpdateDetails_Step_3', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_3::class);
            });
            $this->app->bind('UpdateDetails_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_4::class);
            });
            $this->app->bind('UpdateDetails_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\UpdateDetails_Step_5::class);
            });
         //

         //Client Callers
            $this->app->bind('UpdateDetails_mock', function () {
               return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
            });
         //
      //

      //USSD Survey
			$this->app->bind('Survey', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\Survey::class);
			});
			//Menu Step Handlers
				$this->app->bind('Survey_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_1::class);
				});
				$this->app->bind('Survey_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_2::class);
				});
				$this->app->bind('Survey_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_3::class);
				});
				$this->app->bind('Survey_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\Survey\Survey_Step_5::class);
				});
			//
		//

      //Menu Rate Us
         $this->app->bind('RateUs', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\RateUs::class);
         });
      //

      //Billing Clients
         $this->app->bind('ReceiptingMock', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPaymentMock::class);
         });
			$this->app->bind('MockBillingClient', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\BillingMock::class);
			});            
		//

		//Receipting Payment
			$this->app->bind('MockReceipting', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPaymentMock::class);
			});            
		//

      //USSD Error Response Handlers
         $this->app->bind('INVALIDCONFIRMATION', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidConfirmation::class);
         });
         $this->app->bind('MAINTENANCEMODE', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\MaintenanceMode::class);
         });
         $this->app->bind('INVALIDACCOUNT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAccount::class);
         });
         $this->app->bind('INVALIDAMOUNT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidAmount::class);
         });
         $this->app->bind('CLIENTBLOCKED', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\ClientBlocked::class);
         });
         $this->app->bind('INVALIDINPUT', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidInput::class);
         });
         $this->app->bind('INVALIDSURVEYRESPONSE', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidSurveyResponse::class);
         });
         $this->app->bind('SYSTEMERROR', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\SystemError::class);
         });
         $this->app->bind('WALLETNOTACTIVATED', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\WalletNotActivated::class);
         });
      //

      //Payments Provider Clients
			$this->app->bind('ZAMTEL', function () {
            return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\ZamtelKwacha::class);
				});
			$this->app->bind('AIRTEL', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\AirtelMoney::class);
				});
			$this->app->bind('MTN', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\MTNMoMo::class);
				});
         $this->app->bind('DPOPay', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\DPOPay::class);
				});
         $this->app->bind('MockWallet', function () {
               return $this->app->make(\App\Http\Services\External\PaymentsProviderClients\MockWallet::class);
				});
      //

      //Initiate API Payments
         $this->app->bind('MOMO', function () {
            return $this->app->make(\App\Http\Services\PublicAPI\InitiateMoMoAPIPaymentService::class);
				});
			$this->app->bind('CARD', function () {
               return $this->app->make(\App\Http\Services\PublicAPI\InitiateCardAPIPaymentService::class);
				});
      //

      //SMS Clients
         $this->app->bind('DIAFAANSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\DiafaanSMS::class);
            });
         $this->app->bind('MockSMSDelivery', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MockSMSDelivery::class);
            });
         $this->app->bind('MTNMoMoSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS::class);
            });
         $this->app->bind('CPASSSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\MTNSMS::class);
            });
			$this->app->bind('ZAMTELSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\ZamtelSMS::class);
				});
         $this->app->bind('AIRTELSMS', function () {
               return $this->app->make(\App\Http\Services\External\SMSClients\AirtelSMS::class);
				});
      //

      //Aggregated
         $this->app->bind('AggregatedParentMenu', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\AggregatedParentMenu::class);
         });
         //Clients
            $this->app->bind('AggregatedClient', function () {
               return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\AggregatedClient::class);
            });
         //
      //

      //Tenant ShortCodes
         $this->app->bind('Tenants', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\Tenants::class);
         });
      //

      //Dashboard View Handlers
         $this->app->bind('ConsumerTierView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\ConsumerTierViewService::class);
         });
         $this->app->bind('ConsumerTypeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\ConsumerTypeViewService::class);
         });
         $this->app->bind('DailyByMonthView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\DailyByMonthViewService::class);
         });
         $this->app->bind('DailyCumulativeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\DailyCommulativeViewService::class);
         });
         $this->app->bind('HourlySalesView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\HourlySalesViewService::class);
         });
         $this->app->bind('MonthlyOverYearView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\MonthlyOverYearViewService::class);
         });
         $this->app->bind('PaymentProviderSummaryView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentProviderSummaryViewService::class);
         });
         $this->app->bind('PaymentStatusView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentStatusViewService::class);
         });
         $this->app->bind('PaymentTypeView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentTypeViewService::class);
         });
         $this->app->bind('RevenueCollectorView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenueCollectorViewService::class);
         });
         $this->app->bind('RevenuePointView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenuePointViewService::class);
         });

         $this->app->bind('RevenuePointUserView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\RevenuePointUserViewService::class);
         });
         $this->app->bind('PaymentProviderSummaryUserView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\PaymentProviderSummaryUserViewService::class);
         });
         $this->app->bind('ChannelView', function () {
            return $this->app->make(\App\Http\Services\Analytics\Views\ChannelViewService::class);
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
