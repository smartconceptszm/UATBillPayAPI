<?php 

//UPDATE bulk_messages SET uuidKey = (SELECT uuid());

Step 1, create Tables with Data:
      aggregated_clients
      billing_credentials
      payments_providers
      client_wallets
      client_wallet_credentials



Step 2, Alter Table Payments - Insert the fields:
      "wallet_id"
      "walletNumber"

Step 3, Alter Table Payments - Change Field names:
      "mnoTransactionId" to "ppTransactionId"


Step 4, Alter Table Payments - Populate the wallet_id column:
      Update `payments` 
      SET `wallet_id` = (SELECT `id` FROM `client_wallets` 
                                 WHERE `client_wallets`.`payments_provider_id` = ''
                                       AND `client_wallets`.`client_id` = '')


