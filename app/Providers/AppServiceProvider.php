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
         $this->app->singleton('BalancePostPaid', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\BalancePostPaid::class);
         });
         $this->app->singleton('PayPostPaidBill', function () {
            return $this->app->make(\App\Http\Services\USSD\ShortcutMenus\PayPostPaidBill::class);
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
            return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptPaymentMock::class);
         });
			$this->app->singleton('MockBillingClient', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\BillingMock::class);
			});            
		//

		//Receipting Payment
			$this->app->singleton('MockReceipting', function () {
            return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptPaymentMock::class);
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
         $this->app->singleton('InvalidSurveyResponse', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\InvalidSurveyResponse::class);
         });
         $this->app->singleton('SystemError', function () {
            return $this->app->make(\App\Http\Services\USSD\ErrorResponses\SystemError::class);
         });
         $this->app->singleton('WalletNotActivated', function () {
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
			$this->app->singleton('ZamtelSMS', function () {
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
      
   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }

}
