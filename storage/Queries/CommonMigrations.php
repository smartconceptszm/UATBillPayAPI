<?php 
//UPDATE bulk_messages SET uuidKey = (SELECT uuid());

//Clients Table
   INSERT INTO `billpay_production`.`clients` 
               (`id`,`shortName`,`urlPrefix`,`name`,`smsPayMode`,`surcharge`, 
                  `mode`,`testMSISDN`,`status`,`created_at`,`updated_at`) 
               
   SELECT `uuidKey` AS `id`, `shortName`,`urlPrefix`,`name`,`smsPayMode`,`surcharge`, 
            `mode`,`testMSISDN`,`status`,`created_at`,`updated_at`
   FROM `billpay`.`clients`
//


//MNOs Table
   INSERT INTO `billpay_production`.`mnos` 
      (`id`,`name`,`colour`,`contactName`,`contactEmail`,`contactNo`,`logo`,`created_at`,`updated_at`) 

   SELECT `uuidKey` AS `id`,`name`,`colour`,`contactName`,`contactEmail`,`contactNo`,`logo`,`created_at`,`updated_at`

   FROM `billpay`.`mnos`
//

//Client_menus Table
   //Rood menus
   INSERT INTO `billpay_production`.`client_menus` 
   (`id`,`client_id`,`parent_id`,`order`,`prompt`,`handler`,`billingClient`,`description`,
      `isPayment`,`isDefault`,`isActive`,`receiptingHandler`,`created_at`,`updated_at`) 

   SELECT  `CM`.`uuidKey` AS `id`,`clients`.`uuidKey` AS  `client_id`, '0' AS `parent_id`,`CM`.`order`,
         `CM`.`prompt`,`CM`.`handler`,`CM`.`billingClient`,`CM`.`description`,`CM`.`isPayment`,
         `CM`.`isDefault`,`CM`.`isActive`,`CM`.`receiptingHandler`,`CM`.`created_at`,`CM`.`updated_at`
         
   FROM `billpay`.`client_menus` AS `CM`
      JOIN `billpay`.`clients` AS `clients` ON `CM`.`client_id` = `clients`.`id`
   
   WHERE `CM`.`parent_id` = '0' AND `CM`.`handler` = 'ParentMenu' 

   //All other Menus
   INSERT INTO `billpay_production`.`client_menus` 
   (`id`,`client_id`,`parent_id`,`order`,`prompt`,`handler`,`billingClient`,`description`,
      `isPayment`,`isDefault`,`isActive`,`receiptingHandler`,`created_at`,`updated_at`) 

   SELECT  `CM`.`uuidKey` AS `id`,`clients`.`uuidKey` AS  `client_id`, `CM2`.`uuidKey` AS `parent_id`,`CM`.`order`,
         `CM`.`prompt`,`CM`.`handler`,`CM`.`billingClient`,`CM`.`description`,`CM`.`isPayment`,
         `CM`.`isDefault`,`CM`.`isActive`,`CM`.`receiptingHandler`,`CM`.`created_at`,`CM`.`updated_at`
         
   FROM `billpay`.`client_menus` AS `CM`
      JOIN `billpay`.`clients` AS `clients` ON `CM`.`client_id` = `clients`.`id`
      JOIN `billpay`.`client_menus` AS `CM2` ON `CM`.`parent_id` = `CM2`.`id`





//

//Client_mnos Table
   INSERT INTO `billpay_production`.`client_mnos` 
   (`id`,`client_id`,`mno_id`,`momoActive`,`momoCommission`,`smsCharge`,`momoMode`,`modeMessage`,`created_at`,`updated_at`) 

   SELECT  `CM`.`uuidKey` AS `id`,`clients`.`uuidKey` AS  `client_id`, `mnos`.`uuidKey` AS  `mno_id`,
            `CM`.`momoActive`,`CM`.`momoCommission`,`CM`.`smsCharge`,`CM`.`momoMode`,
            `CM`.`modeMessage`,`CM`.`created_at`,`CM`.`updated_at`
   FROM `billpay`.`client_mnos` AS `CM`
      JOIN `billpay`.`clients` AS `clients` ON `CM`.`client_id` = `clients`.`id`
      JOIN `billpay`.`mnos` AS `mnos` ON `CM`.`mno_id` = `mnos`.`id`
//

