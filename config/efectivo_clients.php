<?php

return [
      'chambeshi'=>[
                  'shortCode'=>'2220',
                  "Home"=>"Welcome to Chambeshi. Enter\n".
                              "1. Pay Water Bill\n".
                              "2. Buy Water Units\n".
                              "3. Check Balance\n".
                              "4. Faults/Complaints\n".
                              "5. Update Details\n".
                              "6. Service Applications\n".
                              "7. Other Payments\n",
                  'menu'=>[
                              "PayBill"=>"1",
                              "BuyUnits"=>"2",
                              "CheckBalance"=>"3",
                              "FaultsComplaints"=>"4",
                              "UpdateDetails"=>"5",
                              "ServiceApplications"=>"6",
                              "OtherPayments"=>"7"
                        ],
                  'hasPrepaid'=>true,
                  'prepaidExample'=>'',
                  'postpaidExample'=>'',
                  'hasUpdateDetailsSubMenu'=>true,
                  'hasOwnSMS'=>false,
                  'hasOwnComplaintsSys'=>false
            ],
      'lukanga'=>[
                  'shortCode'=>'2106',
                  "Home"=>"Welcome to Lukanga Water. Enter\n".
                              "1. Pay Water Bill\n".
                              "2. Check Balance\n".
                              "3. Report Fault/Complaint\n".
                              "4. Update customer details\n",
                  'menu'=>[
                              "PayBill"=>"1",
                              "CheckBalance"=>"2",
                              "FaultsComplaints"=>"3",
                              "UpdateDetails"=>"4"
                        ],
                  'hasPrepaid'=>false,
                  'prepaidExample'=>'',
                  'postpaidExample'=>'',
                  'hasOwnSMS'=>false,
                  'hasOwnComplaintsSys'=>false
            ],
      'swasco'=>[
                  'shortCode'=>'5757',
                  "Home"=>"Welcome to SWASCO. Enter\n".
                              "1. Pay water bill\n".
                              "2. Check balance\n".
                              "3. Report fault/complaint\n".
                              "4. Update details\n".
                              "5. Other payments\n".
                              "6. Survey\n",
                  'menu'=>[
                        "PayBill"=>"1",
                        "CheckBalance"=>"2",
                        "FaultsComplaints"=>"3",
                        "UpdateDetails"=>"4",
                        "OtherPayments"=>"5",
                        "Survey"=>"6"
                  ],
                  'hasPrepaid'=>false,
                  'prepaidExample'=>'',
                  'postpaidExample'=>'liv000xxxx or cho000xxxx',
                  'hasOwnSMS'=>true,
                  'sms_Base_URL'=>'https://apps.zamtel.co.zm/bsms/api/v2.1/action/send/api_key/',
                  'sms_APIKEY' => 'fb122bb2fca7703d8eed1d239e3f349a',
                  'sms_SENDER_ID' => 'SWSC',
                  'remote_Timeout'=>'20', //in seconds
                  'receipting_Timeout'=>'40', //in seconds
            ],

];
