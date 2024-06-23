<?php 

//UPDATE bulk_messages SET uuidKey = (SELECT uuid());
//Sessions Table   - SWASCO

   //Step 1 Update the column 'client_id' in the 'sessions' table
         //with the 'lukanga' 'id' from the migrated (billpay_production.clients')clients table 
         UPDATE `sessions`
         SET `client_id` = '39d62460-7303-11ee-b8ce-fec6e52a2330'
         WHERE `id` >0

   //Step 2 Introduce a field 'mnoId' datatype to 'varchar'
         //Update the mnoId field with keys for each of the MNOs from the migrated (billpay_production.mnos')mnos table 

         //Airtel
         UPDATE `sessions` 
         SET `mnoId`='0fd6f092-730b-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `mno_id`=1;

         //MTN
         UPDATE `sessions` 
         SET `mnoId`='0fd6f718-730b-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `mno_id`=2;

         //ZAMTEL
            UPDATE `sessions` 
            SET `mnoId`='0fd6f90c-730b-11ee-b8ce-fec6e52a2330'
            WHERE `id`>0 AND `mno_id`=3;
   //



   //Step 3 Introduce a field 'menuId' datatype to 'varchar'
      //Update the menuId field with keys for each of the Client_menus from the migrated (billpay_production.client_menus')client_menus table 
         
         //**Counter check the records with NULL menu_id */

         UPDATE `sessions` 
         SET `menuId`='8a2d6ed4-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu`='Home';
   
         UPDATE `sessions` 
         SET `menuId`='8a2d6ab0-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu` = 'CheckBalance';
   
         UPDATE `sessions` 
         SET `menuId`='8a2d6998-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu`='PayBill';
   
      //
   //Step 4 //District - N/A

   //Step 5 Create 'uuidKey' field in the 'swasco'.'sessions' Table
      //Populate the field with unique keys.
      UPDATE `swascoussd`.`sessions` SET `uuidKey` = (SELECT uuid());
   //

   //Step 6 Update 'paymentAmount' field to 0
      UPDATE `sessions`  
      SET `sessions`.`paymentAmount` = 0
      WHERE `id` > 0 AND `sessions`.`paymentAmount` IS NULL;
   //

   //Step 7 Import Data into Sessions table
      INSERT INTO `billpay_production`.`sessions` 
         (`id`,`client_id`,`mno_id`,`menu_id`,`sessionId`,`customerJourney`,
            `mobileNumber`, `accountNumber`,`district`,`paymentAmount`,
            `status`,`error`,`created_at`,`updated_at`) 
                     
      SELECT `S1`.`uuidKey` AS `id`, '39d62460-7303-11ee-b8ce-fec6e52a2330', `S1`.`mnoId` AS `mno_id`,`S1`.`menuId` AS `menu_id`,
               `S1`.`sessionId`, `S1`.`subscriberInput` AS `customerJourney`,
               `S1`.`mobileNumber`,
               `S1`.`accountNo` AS `accountNumber`,`S1`.`district`, 
               `S1`.`paymentAmount`,`S1`.`status`,`S1`.`errorMessage` AS `error`,
               `S1`.`created_at`,`S1`.`updated_at`
               
      FROM `efectivobillpay`.`sessions` AS `S1`
   //
//

// Payments From lukanga
   INSERT INTO `billpay_production`.`payments`(
               `id`,`client_id`,`session_id`,`mno_id`,`menu_id`,
               `mobileNumber`,`accountNumber`,`district`,
               `ppTransactionId`,`paymentAmount`,
               `receiptAmount`,`transactionId`,
               `receiptNumber`,`receipt`,`channel`,
               `paymentStatus`,`status`,`error`,
               `created_at`,`updated_at`
            )
   SELECT
      uuid(),`S1`.`client_id`,`S1`.`id` AS `session_id`,`S1`.`mno_id`,`S1`.`menu_id`,
      `S1`.`mobileNumber`,`S1`.`accountNumber`,`S1`.`district`,`S2`.`ppTransactionId`,
      `S1`.`paymentAmount`, `S1`.`paymentAmount` AS `receiptAmount`, `S2`.`transactionId`, 
      `S2`.`receiptNo` AS `receiptNumber`,`S2`.`receipt`,
      'USSD' AS `channel`,`S2`.`paymentStatus`, `S1`.`status`,`S1`.`error`,
      `S1`.`created_at`,`S1`.`updated_at`
   FROM `billpay_production`.`sessions` AS `S1`
         JOIN `efectivobillpay`.`sessions` AS `S2` ON `S1`.`id` = `S2`.`uuidKey`
   WHERE
      `S1`.`menu_id` IN ('8a2d6998-7306-11ee-b8ce-fec6e52a2330') 
            AND `S1`.`paymentAmount` > 0  AND `S1`.`accountNumber` IS NOT NULL

//




//Groups/Roles Table

   UPDATE `efectivobillpay`.`groups` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`groups` 
   (`id`,`client_id`,`name`,`description`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`, '39d62460-7303-11ee-b8ce-fec6e52a2330' AS `client_id`,`name`,`description`,`created_at`,`updated_at`
   FROM `efectivobillpay`.`groups` WHERE `client_id` = 2
//

//Users Table

   UPDATE `efectivobillpay`.`users` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`users` 
   (`id`,`client_id`,`username`,`password`,`fullnames`,`mobileNumber`,`email`,`status`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`, '39d62460-7303-11ee-b8ce-fec6e52a2330' AS `client_id`,
            `username`,`password`,`fullnames`,`mobileNumber`,`email`,`status`,
            `created_at`,`updated_at`
   FROM `efectivobillpay`.`users` where `mobileNumber` IS NOT NULL


//

//Users groups



   INSERT INTO `billpay_production`.`user_groups` 
   (`id`,`user_id`,`group_id`,`created_at`,`updated_at`) 
   
   SELECT (SELECT uuid()) AS `id`, `id` AS `user_id`, 'c6a3b4bc-805e-11ee-b045-d310d7089dfe' AS `group_id`,
      `created_at`,`updated_at`

   FROM `billpay_production`.`users` 
   WHERE `client_id` = '39d62460-7303-11ee-b8ce-fec6e52a2330'



//

//Group Rights
   INSERT INTO `billpay_production`.`group_rights` 
                  (`id`,`group_id`,`right_id`) 

   SELECT uuid(), 'c6a3b34a-805e-11ee-b045-d310d7089dfe' AS `group_id`, `id` AS `right_id`
   FROM `billpay_production`.`rights` 
//