<?php 
//Sessions.
   INSERT INTO `billpay`.`messages` (`id`,`mobileNumber`,`accountNumber`,`message`,`mno_id`,
                     `client_id`,`bulk_id`,`transaction_id`,`amount`,`type`,
                     `status`,`user_id`,`error`,`created_at`,`updated_at`) 
                                       
   SELECT `id`,`mobileNumber`,NULL,`message`,`mno_id`,`client_id`,NULL,NULL,
               `amount`,`type`,`status`,`user_id`,`errorMessage`,`created_at`,`updated_at`
   FROM `efectivobillpay`.`messages`;
//
