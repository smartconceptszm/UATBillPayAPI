UPDATE `client_menus`

SET `billingClient` = 'mazabukaRemoteCustomerAccount', `receiptingHandler` = 'ReceiptMazabukaOnCustomerAccount'

WHERE `id` IN(
    
SELECT `cm`.`id`
FROM `client_menus_temp` AS `cm`
	INNER JOIN `client_menus_temp` AS `cm2` ON `cm`.`parent_id` = `cm2`.`id`
    INNER JOIN `client_menus_temp` AS `cm3` ON `cm2`.`parent_id` = `cm3`.`id`
WHERE 	`cm2`.`order` IN (1,2) 
		AND 
     	`cm`.`client_id` = '39d62960-7303-11ee-b8ce-fec6e52a2330');




        UPDATE `client_menus`

SET `billingClient` = 'mazabukaLocalCommonAccount', `receiptingHandler` = 'ReceiptMazabukaOnCommonAccount'

WHERE `id` IN(
    
SELECT `cm`.`id`
FROM `client_menus_temp` AS `cm`
	INNER JOIN `client_menus_temp` AS `cm2` ON `cm`.`parent_id` = `cm2`.`id`
    INNER JOIN `client_menus_temp` AS `cm3` ON `cm2`.`parent_id` = `cm3`.`id`
WHERE 	`cm2`.`order` > 2
		AND 
     	`cm`.`client_id` = '39d62960-7303-11ee-b8ce-fec6e52a2330');
