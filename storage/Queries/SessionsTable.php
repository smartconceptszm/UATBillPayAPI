<?php 
//Sessions.
   INSERT INTO `billpay`.`sessions` (`id`,`sessionId`,`mobileNumber`, `mno_id`,`client_id`,`menu`,
                                    `customerJourney`,`accountNumber`,`district`,
                              `status`,`error`,`created_at`,`updated_at`) 
                                       
   SELECT `id`,`sessionId`,`mobileNumber`, `mno_id`,`client_id`,`menu`,
            `subscriberInput` as `customerJourney`,SUBSTR(`accountNo`, 1, 20) as `accountNumber`,
            `district`,`status`,`errorMessage` as `error`,`created_at`,`updated_at`
   FROM `efectivobillpay`.`sessions`;
//

// Payments
   INSERT INTO `billpay`.`payments`(
      `client_id`,
      `session_id`,
      `mno_id`,
      `menu_id`,
      `mobileNumber`,
      `accountNumber`,
      `district`,
      `reference`,
      `mnoTransactionId`,
      `surchargeAmount`,
      `paymentAmount`,
      `receiptAmount`,
      `transactionId`,
      `receiptNumber`,
      `receipt`,
      `channel`,
      `paymentStatus`,
      `status`,
      `error`,
      `created_at`,
      `updated_at`
   )
   SELECT
      `S1`.`client_id`,
      `S2`.`id` AS `session_id`,
      `S1`.`mno_id`,
      `S2`.`menu_id`,
      `S1`.`mobileNumber`,
      SUBSTR(`S1`.`accountNo`, 1, 20) AS `accountNumber`,
      `S1`.`district`,
      `S1`.`mnoTransactionId`,
      `S1`.`surchargeAmount`,
      `S1`.`receiptAmount` AS `paymentAmount`,
      `S1`.`receiptAmount`,
      `S1`.`transactionId`,
      `S1`.`receiptNo` AS `receiptNumber`,
      `S1`.`receipt`,
      'USSD' AS `channel`,
      `S1`.`paymentStatus`,
      `S1`.`status`,
      `S1`.`errorMessage` AS `error`,
      `S1`.`created_at`,
      `S1`.`updated_at`
   FROM
      `efectivobillpay`.`sessions` AS `S1`
   JOIN `billpay`.`sessions` AS `S2`
   ON
      `S1`.`sessionId` = `S2`.`sessionId`
   WHERE
      `S1`.`menu` = 'PayBill' AND `S1`.`paymentAmount` IS NOT NULL AND `S1`.`accountNo` IS NOT NULL
//
      