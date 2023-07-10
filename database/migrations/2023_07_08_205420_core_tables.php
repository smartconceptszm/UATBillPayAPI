<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * Run the migrations.
    */
   public function up(): void
   {

      Schema::create('sessions', function (Blueprint $table) {
         $table->id();
         $table->string('sessionId',50)->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->unsignedBigInteger('mno_id')->nullable();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->enum('menu',['Home','PayBill','BuyUnits','CheckBalance',
                              'FaultsComplaints','UpdateDetails',
                              'ServiceApplications','OtherPayments']
                           )->default('Home')->notNullable();
         $table->string('customerJourney')->notNullable();
         $table->string('accountNumber',20)->nullable();
         $table->string('district',50)->nullable();
         $table->string('response',160)->nullable();
         $table->enum('status',['INITIATED','COMPLETED','FAILED','REVIEWED',
                                       'MANUALLY REVIEWED'])->default('INITIATED')->notNullable();
         $table->text('error')->nullable();
         $table->timestamps();
         $table->unique(['sessionId', 'mobileNumber'],'session_mobileNumber');
      });

      Schema::create('payments', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('session_id')->nullable();
         $table->unsignedBigInteger('mno_id')->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('mnoTransactionId',30)->nullable();            
         $table->float('surchargeAmount',10,2)->default(0);
         $table->float('paymentAmount',10,2)->default(0);
         $table->float('receiptAmount',10,2)->default(0);
         $table->string('transactionId',50)->nullable();
         $table->string('receiptNumber',30)->nullable();
         $table->string('receipt',160)->nullable();
         $table->enum('channel',['USSD','MOBILEAPP','BANKAPI', 'WEBSITE'])
                              ->default('USSD')->notNullable();
         $table->enum('paymentStatus',['INITIATED','SUBMISSION FAILED','SUBMITTED',
                              'PAYMENT FAILED','PAID | NOT RECEIPTED','RECEIPTED',
                              'RECEIPT DELIVERED'])
                              ->default('INITIATED')->notNullable();
         $table->enum('status',['INITIATED','COMPLETED','REVIEWED',
               'MANUALLY REVIEWED'])->default('INITIATED')->notNullable();
         $table->string('error',255)->nullable();
         $table->unsignedBigInteger('user_id')->nullable();
         $table->timestamps();
         $table->unique(['session_id', 'mobileNumber'],'session_mobileNumber');
         $table->index(['client_id', 'paymentStatus', 'created_at']);
      });

      Schema::create('mnos', function (Blueprint $table) {
         $table->id();
         $table->string('name')->unique()->notNullable();
         $table->string('colour')->nullable();
         $table->string('contactName')->nullable();
         $table->string('contactEmail')->nullable();
         $table->string('contactNo')->nullable();
         $table->string('logo')->nullable();
         $table->timestamps();
      });

      Schema::create('clients', function (Blueprint $table) {
         $table->id();
         $table->string('code',10)->unique()->notNullable();
         $table->string('shortname',25)->unique()->notNullable();
         $table->string('urlPrefix',25)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->float('balance',10,2)->default(0);
         $table->enum('smsPayMode',['POST-PAID','PRE-PAID'])->default('POST-PAID')->notNullable();
         $table->enum('surcharge',['NO','YES'])->default('NO')->notNullable();
         $table->enum('mode',['UP','DOWN'])->default('UP')->notNullable();
         $table->enum('status',['ACTIVE','BLOCKED'])->default('ACTIVE')->notNullable();
         $table->timestamps();
      });

      Schema::create('mnocharges', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->unsignedBigInteger("mno_id")->notNullable();
         $table->float('momoCommission',10,2)->default(0);
         $table->float('smsCharge',10,2)->default(0);
         $table->timestamps();
      });

      Schema::create('topups', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->float('amount',10,2)->default(0);
         $table->enum('approval_status',['PENDING','APPROVED','REJECTED'])->default('PENDING')->notNullable();
         $table->unsignedBigInteger('initiatedBy')->notNullable();
         $table->unsignedBigInteger('approvedBy')->notNullable();
         $table->timestamps();
      });

      Schema::create('messages', function (Blueprint $table) {
         $table->id();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('message',160)->notNullable();
         $table->unsignedBigInteger("mno_id")->notNullable();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->unsignedBigInteger('bulk_id')->nullable();
         $table->string('transaction_id',50)->nullable();
         $table->float('amount',10,2)->default(0);
         $table->enum('type',['RECEIPT','SINGLE','BULK','BULKCUSTOM','NOTIFICATION'])->default('RECEIPT')->notNullable();
         $table->enum('status',['INITIATED','DELIVERED','FAILED'])->default('INITIATED')->notNullable();
         $table->unsignedBigInteger('user_id')->nullable();
         $table->text('error')->nullable();
         $table->timestamps();
      });

      Schema::create('bulk_messages', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->unsignedBigInteger("user_id")->notNullable();
         $table->string('sourceFile')->nullable();
         $table->string('description')->nullable();
         $table->enum('type',['BULK','BULKCUSTOM'])->default('BULK')->notNullable();
         $table->timestamps();
     });

      Schema::create('customers', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->unique(['client_id','accountNumber', 'mobileNumber'],'accountNumber_mobileNumber');
         $table->timestamps();
      });

      Schema::create('complaint_types', function (Blueprint $table) {
         $table->id();
         $table->string('code',2)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->timestamps();
      });
      
      Schema::create('complaint_subtypes', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('complaint_type_id')->notNullable();
         $table->string('code',3)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->enum('requiresDetails',['YES','NO'])->default('NO');
         $table->enum('detailType',['MOBILE','READING','METER','PAYMENTMODE'])->nullable();
         $table->string('prompt')->nullable();
         $table->timestamps();
      });

      Schema::create('client_complaint_types', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('complaint_type_id')->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
         $table->unique(['client_id', 'complaint_type_id'],'client_complaint_type');
         $table->unique(['client_id', 'complaint_type_id', 'order'],'client_type_order');
      });

      Schema::create('client_complaint_subtypes', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('complaint_subtype_id')->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
         $table->unique(['client_id','complaint_subtype_id'],'client_subtype');
         $table->unique(['client_id','complaint_subtype_id', 'order'],'client_subtype_order');
      });

      Schema::create('complaints', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('complaint_subtype_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('address',255)->nullable();
         $table->string('details')->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
         $table->unsignedBigInteger('assignedBy')->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
      });

      Schema::create('otherpayment_types', function (Blueprint $table) {
         $table->id();
         $table->string('code',3)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->enum('receiptAccount',['CUSTOMER','GENERAL LEDGER'])->default('CUSTOMER')->notNullable();
         $table->enum('hasApplicationNo',['NO','YES'])->default('NO')->notNullable();
         $table->timestamps();
      });

      Schema::create('client_otherpayment_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->notNullable();
            $table->unsignedBigInteger('payment_type_id')->notNullable();
            $table->unsignedTinyInteger('order')->unique()->notNullable();
            $table->string('ledgerAccountNumber',50)->notNullable();
            $table->string('prompt',250)->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'payment_type_id'],'client_payment_type');
      });

      Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->unique()->notNullable();
            $table->enum('type',['MOBILE','GENERAL'])->default('GENERAL')->notNullable();
            $table->string('format',150)->unique()->nullable();
            $table->timestamps();
      });

      Schema::create('client_customer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->notNullable();
            $table->unsignedBigInteger('customer_detail_id')->notNullable();
            $table->unsignedTinyInteger('order')->unique()->notNullable();
            $table->string('prompt',250)->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'customer_detail_id'],'client_customer_detail');
      });

      Schema::create('customer_updates', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('customer_detail_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('address',255)->nullable();
         $table->string('details')->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
         $table->unsignedBigInteger('assignedBy')->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
      });

      Schema::create('service_types', function (Blueprint $table) {
         $table->id();
         $table->string('name',50)->unique()->notNullable();
         $table->enum('onExistingAccount',['YES','NO'])->default('NO')->notNullable();
         $table->string('description',250)->nullable();
         $table->timestamps();
      });
      
      Schema::create('client_service_types', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('service_type_id')->notNullable();
         $table->unsignedTinyInteger('order')->unique()->notNullable();
         $table->timestamps();
         $table->unique(['client_id', 'service_type_id'],'client_service_type');
      });
      
      Schema::create('client_service_type_details', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_service_type_id')->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->enum('type',['MOBILE','NATIONALID','GENERAL'])->default('GENERAL')->notNullable();
         $table->string('prompt',150)->nullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
         $table->unique(['client_service_type_id', 'order'],'client_service_type_detail');
      });
      
      Schema::create('service_applications', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('service_type_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->nullable();
         $table->enum('status',['SUBMITTED','ASSIGNED','CLOSED'])->default('SUBMITTED')->notNullable();
         $table->unsignedBigInteger('assignedBy')->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
      });
      
      Schema::create('service_application_details', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('service_application_id')->notNullable();
         $table->unsignedBigInteger('service_type_detail_id')->notNullable();
         $table->string('value')->nullable();
         $table->timestamps();
      });

      Schema::create('surveys', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->string('description',250)->nullable();
         $table->unsignedTinyInteger('order')->unique()->notNullable();
         $table->timestamps();
      });
   
      Schema::create('survey_questions', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('survey_id')->notNullable();
         $table->string('prompt',155)->notNullable();
         $table->enum('type',['MOBILE','LIST','NATIONALID','ONEWORD','GENERAL'])->default('GENERAL')->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
      });
   
      Schema::create('survey_question_lists', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('survey_question_id')->notNullable();
         $table->string('prompt',155)->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
      });
   
      Schema::create('survey_entries', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('survey_id')->notNullable();
         $table->string('caseNumber',50)->unique()->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->enum('status',['INITIATED','SUBMITTED','ASSIGNED','CLOSED'])->default('INITIATED')->notNullable();
         $table->unsignedBigInteger('assignedBy')->nullable();
         $table->string('assignedTo')->nullable();
         $table->string('resolution')->nullable();
         $table->string('comments')->nullable();
         $table->timestamps();
      });
   
      Schema::create('survey_entry_details', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('survey_entry_id')->notNullable();
         $table->unsignedBigInteger('survey_question_id')->notNullable();
         $table->string('answer')->notNullable();
         $table->timestamps();
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {

      Schema::dropIfExists('sessions');
      Schema::dropIfExists('mnos');
      Schema::dropIfExists('clients');
      Schema::dropIfExists('mnocharges');
      Schema::dropIfExists('topups');
      Schema::dropIfExists('messages');
      Schema::dropIfExists('bulk_messages');
      Schema::dropIfExists('customers');
      Schema::dropIfExists('payments');
      Schema::dropIfExists('client_complaint_subtypes');
      Schema::dropIfExists('client_complaint_types');
      Schema::dropIfExists('complaint_subtypes');
      Schema::dropIfExists('complaint_types');
      Schema::dropIfExists('complaints');
      Schema::dropIfExists('payment_types');
      Schema::dropIfExists('client_payment_types');
      Schema::dropIfExists('customer_details');
      Schema::dropIfExists('client_customer_details');
      Schema::dropIfExists('customer_updates');
      Schema::dropIfExists('service_application_details');
      Schema::dropIfExists('client_service_type_details');
      Schema::dropIfExists('client_service_types');
      Schema::dropIfExists('service_applications');
      Schema::dropIfExists('service_types');
      Schema::dropIfExists('surveys');
      Schema::dropIfExists('survey_questions');
      Schema::dropIfExists('survey_question_lists');
      Schema::dropIfExists('survey_entries');
      Schema::dropIfExists('survey_entry_details');

   }
};
