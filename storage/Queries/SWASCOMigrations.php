<?php 

//UPDATE bulk_messages SET uuidKey = (SELECT uuid());
//Sessions Table   - SWASCO

   //Step 1 Create the column 'client_id' in the 'sessions' table
         //Update the value with the 'swasco' 'id' from the migrated (billpay_production.clients')clients table 
         UPDATE `sessions`
         SET `client_id` = '39d6269a-7303-11ee-b8ce-fec6e52a2330'
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

         //MTN
         UPDATE `sessions` 
         SET `mnoId`='0fd6f90c-730b-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `mno_id`=3;
   //
   //Step 3 Introduce a field 'menuId' datatype to 'varchar'
      //Update the menuId field with keys for each of the Client_menus from the migrated (billpay_production.client_menus')client_menus table 
         
         //**Counter check the records with NULL menu_id */
         UPDATE `sessions` 
         SET `menuId`='8a2d70d2-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND ISNULL (`menu_id`);
   
         UPDATE `sessions` 
         SET `menuId`='8a2d5df4-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu_id`=1;
   
         UPDATE `sessions` 
         SET `menuId`='8a2d63a8-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu_id` = 2;
   
         UPDATE `sessions` 
         SET `menuId`='8a2d6524-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu_id`=3;
   
         UPDATE `sessions` 
         SET `menuId`='8a2d686c-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu_id`=4;
   
         UPDATE `sessions` 
         SET `menuId`='8a2d6646-7306-11ee-b8ce-fec6e52a2330'
         WHERE `id`>0 AND `menu_id`=5;
      //
   //Step 4 //Create new Field District
      //Update the 'district' field  with  data
         UPDATE `sessions` SET `swascoAccountNo`=NULL WHERE `swascoAccountNo`='0';

         UPDATE `sessions`
         SET district = "BATOKA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='BAT';

         UPDATE `sessions`
         SET district = "CHISEKESI"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='CHI';

         UPDATE `sessions`
         SET district = "CHIKANKATA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='CHK';

         UPDATE `sessions`
         SET district = "CHOMA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='CHO';

         UPDATE `sessions`
         SET district = "CHIRUNDU"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='CHR';

         UPDATE `sessions`
         SET district = "GWEMBE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`,1, 3)='GWE';

         UPDATE `sessions`
         SET district = "KALOMO"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='KAL';

         UPDATE `sessions`
         SET district = "KAZUNGULA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='KAZ';

         UPDATE `sessions`
         SET district = "LIVINGSTONE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='LIV';

         UPDATE `sessions`
         SET district = "MAGOYE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MAG';

         UPDATE `sessions`
         SET district = "MAZABUKA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MAZ';

         UPDATE `sessions`
         SET district = "MAAMBA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MAB';

         UPDATE `sessions`
         SET district = "MBABALA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MBL';

         UPDATE `sessions`
         SET district = "MUNYUMBWE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MUN';

         UPDATE `sessions`
         SET district = "MONZE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='MZE';

         UPDATE `sessions`
         SET district = "NAMWALA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='NAM';

         UPDATE `sessions`
         SET district = "NEGANEGA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='NEG';

         UPDATE `sessions`
         SET district = "PEMBA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='PEM';

         UPDATE `sessions`
         SET district = "SIAVONGA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='SIA';

         UPDATE `sessions`
         SET district = "SINAZEZE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='SZE';

         UPDATE `sessions`
         SET district = "SINAZONGWE"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='SIN';

         UPDATE `sessions`
         SET district = "ZIMBA"
         WHERE `id`>0 and SUBSTRING(`swascoAccountNo`, 1, 3)='ZIM';
      //
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
                     
      SELECT `S1`.`uuidKey` AS `id`, `S1`.`client_id`, `S1`.`mnoId` AS `mno_id`,`S1`.`menuId` AS `menu_id`,
               `S1`.`sessionId`, `S1`.`subscriberInput` AS `customerJourney`,
               SUBSTRING(`S1`.`phone_number`, 2, 12) AS `mobileNumber`,
               `S1`.`swascoAccountNo` AS `accountNumber`,`S1`.`district`, 
               `S1`.`paymentAmount`,`S1`.`status`,`S1`.`errorMessage` AS `error`,
               `S1`.`created_at`,`S1`.`updated_at`
               
      FROM `swascoussd`.`sessions` AS `S1`
   //
//

// Payments From SWASCO
   INSERT INTO `billpay_production`.`payments`(
               `id`,`client_id`,`session_id`,`mno_id`,`menu_id`,
               `mobileNumber`,`accountNumber`,`district`,
               `mnoTransactionId`,`paymentAmount`,
               `receiptAmount`,`transactionId`,
               `receiptNumber`,`receipt`,`channel`,
               `paymentStatus`,`status`,`error`,
               `created_at`,`updated_at`
            )
   SELECT
      uuid(),`S1`.`client_id`,`S1`.`id` AS `session_id`,`S1`.`mno_id`,`S1`.`menu_id`,
      `S1`.`mobileNumber`,`S1`.`accountNumber`,`S1`.`district`,`S2`.`mnoTransactionId`,
      `S1`.`paymentAmount`, `S1`.`paymentAmount` AS `receiptAmount`, `S2`.`transactionId`, 
      `S2`.`swascoReceiptNo` AS `receiptNumber`,`S2`.`customerReceipt` AS `receipt`,
      'USSD' AS `channel`,`S2`.`paymentStatus`, `S1`.`status`,`S1`.`error`,
      `S1`.`created_at`,`S1`.`updated_at`
   FROM `billpay_production`.`sessions` AS `S1`
         JOIN `swascoussd`.`sessions` AS `S2` ON `S1`.`id` = `S2`.`uuidKey`
   WHERE
      `S1`.`menu_id` IN ('8a2d5df4-7306-11ee-b8ce-fec6e52a2330',
                           '8a2d6646-7306-11ee-b8ce-fec6e52a2330',
                           '8a2d70d2-7306-11ee-b8ce-fec6e52a2330') 
            AND `S1`.`paymentAmount` > 0  AND `S1`.`accountNumber` IS NOT NULL
//

//Groups/Roles Table

   UPDATE `swascoussd`.`groups` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`groups` 
   (`id`,`client_id`,`name`,`description`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`, '39d6269a-7303-11ee-b8ce-fec6e52a2330' AS `client_id`,`name`,`description`,`created_at`,`updated_at`
   FROM `swascoussd`.`groups`
//

//Rights Table
   //Get new rights from billpay database
   INSERT INTO `billpay_production`.`rights` 
   (`id`,`name`,`description`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`,`name`,`description`,`created_at`,`updated_at`
   FROM `billpay`.`rights`
//

//Users Table

   UPDATE `swascoussd`.`users` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`users` 
   (`id`,`client_id`,`username`,`password`,`fullnames`,`mobileNumber`,`email`,`status`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`, '39d6269a-7303-11ee-b8ce-fec6e52a2330' AS `client_id`,
            `username`,`password`,`fullnames`,`mobileNo` AS `mobileNumber`,`email`,`status`,
            `created_at`,`updated_at`
   FROM `swascoussd`.`users` where `mobileNo` IS NOT NULL

//

//Users groups
   UPDATE `swascoussd`.`user_groups` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`user_groups` 
                  (`id`,`user_id`,`group_id`,`created_at`,`updated_at`) 

   SELECT `ug`.`uuidKey` AS `id`, `u2`.`id` AS `user_id`, `g`.`uuidKey` AS `group_id`,
            `ug`.`created_at`,`ug`.`updated_at`
   FROM `swascoussd`.`user_groups` AS `ug` 
         JOIN `swascoussd`.`groups` AS `g` ON `g`.`id` = `ug`.`group_id`
         JOIN `swascoussd`.`users` AS `u1` ON `u1`.`id` = `ug`.`user_id`
         JOIN  `billpay_production`.`users`  AS `u2` ON `u2`.`username` = `u1`.`username`
//

//Users groups
   UPDATE `swascoussd`.`user_groups` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`user_groups` 
                  (`id`,`user_id`,`group_id`,`created_at`,`updated_at`) 

   SELECT `ug`.`uuidKey` AS `id`, `u2`.`id` AS `user_id`, `g`.`uuidKey` AS `group_id`,
            `ug`.`created_at`,`ug`.`updated_at`
   FROM `swascoussd`.`user_groups` AS `ug` 
         JOIN `swascoussd`.`groups` AS `g` ON `g`.`id` = `ug`.`group_id`
         JOIN `swascoussd`.`users` AS `u1` ON `u1`.`id` = `ug`.`user_id`
         JOIN  `billpay_production`.`users`  AS `u2` ON `u2`.`username` = `u1`.`username`
//

//Group Rights
   INSERT INTO `billpay_production`.`group_rights` 
                  (`id`,`group_id`,`right_id`) 

   SELECT uuid(), 'fe777b46-77b5-11ee-b8ce-fec6e52a2330' AS `group_id`, `id` AS `right_id`
   FROM `billpay_production`.`rights` 
//

//Shortcut Customers
   UPDATE `swascoussd`.`customers` SET uuidKey = (SELECT uuid());

   INSERT INTO `billpay_production`.`shortcut_customers`
               (`id`,`client_id`,`mobileNumber`, `accountNumber`,`created_at`,`updated_at`) 
        
   SELECT `uuidKey` AS `id`,'39d6269a-7303-11ee-b8ce-fec6e52a2330' AS `client_id`, SUBSTRING(`phone_number`, 2, 12) AS `mobileNumber`,
         `AccountNo` AS `accountNumber`, `created_at`,`updated_at`
   FROM `swascoussd`.`customers`
//