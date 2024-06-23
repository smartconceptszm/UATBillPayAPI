<?php 

// Set client_id column to SWASCO
      UPDATE `sessions`
      SET `client_id` = 3
      WHERE `id` >0

// Create new field 'menu' of varcher 50
      UPDATE `sessions` 
      SET `menu`='Home'
      WHERE `id`>0 AND ISNULL (`menu_id`);

      UPDATE `sessions` 
      SET `menu`='PayBill'
      WHERE `id`>0 AND `menu_id`=1;

      UPDATE `sessions` 
      SET `menu`='CheckBalance'
      WHERE `id`>0 AND `menu_id`=2;

      UPDATE `sessions` 
      SET `menu`='FaultsComplaints'
      WHERE `id`>0 AND `menu_id`=3;

      UPDATE `sessions` 
      SET `menu`='UpdateDetails'
      WHERE `id`>0 AND `menu_id`=4;

      UPDATE `sessions` 
      SET `menu`='OtherPayments'
      WHERE `id`>0 AND `menu_id`=5;

// Change field type to enum 

//Delete old menu_id field

//Change field from `swascoAccountNo` to `accountNo`

//Create new Field District

//Update field district with  data

      UPDATE `sessions` SET `accountNo`=NULL WHERE `accountNo`='0';

      UPDATE `sessions`
      SET district = "BATOKA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='BAT';

      UPDATE `sessions`
      SET district = "CHISEKESI"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='CHI';

      UPDATE `sessions`
      SET district = "CHIKANKATA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='CHK';

      UPDATE `sessions`
      SET district = "CHOMA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='CHO';

      UPDATE `sessions`
      SET district = "CHIRUNDU"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='CHR';

      UPDATE `sessions`
      SET district = "GWEMBE"
      WHERE `id`>0 and SUBSTRING(`accountNo`,1, 3)='GWE';

      UPDATE `sessions`
      SET district = "KALOMO"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='KAL';

      UPDATE `sessions`
      SET district = "KAZUNGULA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='KAZ';

      UPDATE `sessions`
      SET district = "LIVINGSTONE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='LIV';

      UPDATE `sessions`
      SET district = "MAGOYE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MAG';

      UPDATE `sessions`
      SET district = "MAZABUKA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MAZ';

      UPDATE `sessions`
      SET district = "MAAMBA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MAB';

      UPDATE `sessions`
      SET district = "MBABALA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MBL';

      UPDATE `sessions`
      SET district = "MUNYUMBWE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MUN';

      UPDATE `sessions`
      SET district = "MONZE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='MZE';

      UPDATE `sessions`
      SET district = "NAMWALA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='NAM';

      UPDATE `sessions`
      SET district = "NEGANEGA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='NEG';

      UPDATE `sessions`
      SET district = "PEMBA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='PEM';

      UPDATE `sessions`
      SET district = "SIAVONGA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='SIA';

      UPDATE `sessions`
      SET district = "SINAZEZE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='SZE';

      UPDATE `sessions`
      SET district = "SINAZONGWE"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='SIN';

      UPDATE `sessions`
      SET district = "ZIMBA"
      WHERE `id`>0 and SUBSTRING(`accountNo`, 1, 3)='ZIM';
//Change field from `swascoReceiptNo` to `receiptNo`

//Change field from `customerReceipt` to `receipt`              



//Import Payment into NEW 'Receipt' Table
      INSERT INTO `receipts` (`client_id`, `session_id`, `mno_id`,
                  `mobileNumber`,`accountNo`,`district`,`ppTransactionId`,
                  `surchargeAmount`,`paymentAmount`,`receiptAmount`,
                  `transactionId`,`receiptNo`,`receipt`,`channel`,
                  `paymentStatus`,`status`,`error`)                           
      SELECT `client_id`,`id` AS `session_id`, `mno_id`,`mobileNumber`,`accountNo`,
            `district`,`ppTransactionId`,`surchargeAmount`,`paymentAmount`,
            `receiptAmount`,`transactionId`,`receiptNo`,`receipt`,
            'USSD' as `channel`,`paymentStatus`,`status`,`errorMessage` as `error`
      FROM `sessions`
      WHERE `sessions`.`menu`='PayBill' AND `sessions`.`paymentAmount` IS NOT null 
            AND `sessions`.`accountNo` IS NOT Null;

//Complaint types

      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('01','BILLING');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('02','LEAKAGE');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('03','METER');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('04','WATER QUALITY');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('05','WATER SUPPLY');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('06','PRE-PAID METER');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('07','SEWERAGE');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('08','Service Request Related');
      INSERT INTO `complaint_types`  (`code`,`name`) VALUES ('09','OTHERS');


//Complaint Sub Type
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01A','Not Receiving Bills','YES','MOBILE','Enter mobile number to receive the bill (e.g. 097xxxxxxx):');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01B','Billed but no connection','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01C','Bill too high','YES','READING','Enter the current meter reading:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01D','Billed Fixed but metered','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01E','Bill incorrect','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (1,'01F','Payment not reflected','YES','PAYMENTMODE','Enter the mode of payment:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (2,'05A','No water','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (2,'05B','Pressure too low','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (2,'05C','Pressure too high','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (2,'05D','Erratic water supply','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (3,'02A','Pipe Burst','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (3,'02B','High pressure Leakage','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (3,'02C','Low pressure Leakage','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07A','Blocked sewer line','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07B','Broken sewer pipe','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07C','Broken sewer manhole','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07D','Overflowing sewer manhole','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07E','Sewer Surcharge','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07F','Sewer Contamination','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (4,'07G','Missing Sewer Manhole','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03A','Meter reading too high','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03B','Demand for meter','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03C','Request for meter testing','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03D','leaking meter','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03E','Meter broken/Leaking','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03F','Leakage before or at the meter','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03G','Meter reading in reverse','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03H','Meter stolen','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03I','Incorrect meter readings','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (5,'03J','Stuck meter/Fauty meter','YES','METER','Enter the meter number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (6,'04A','Water has colour','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (6,'04B','Water has strong smell','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (6,'04C','Water has particles','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (6,'04D','Water has an aftertaste','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08A','New Water Connection','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08B','Account Opening','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08C','Reconnection','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08D','Request For Disconnection','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08E','Meter Separation','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08F','Leak Detection','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08G','Meter Testing','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08H','Close Account','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (7,'08I','Meter Installation','YES','APPLICATION','Enter the application number:');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (8,'09A','Backfilling Trenches After Repair','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (8,'09B','Wrongful Disconnection','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (8,'09C','Change of Property Details','NO',NULL,'');
      INSERT INTO `complaint_subtypes` (`complaint_type_id`,`code`,`name`,`requiresDetails`,`detailType`,`prompt`) VALUES (8,'09D','Repair of Pavement','NO',NULL,'');
//Lukanga Complaint Types
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,1,1);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,2,2);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,3,3);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,4,4);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,5,5);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,6,6);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,7,7);
      INSERT INTO `complaint_types_order` (`client_id`,`complaint_type_id`,`order`) VALUES (2,8,8);

//Lukanga Complaint Types
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,1,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,2,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,3,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,4,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,5,5);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,6,6);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,7,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,8,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,9,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,10,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,11,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,12,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,13,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,14,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,15,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,16,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,17,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,18,5);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,19,6);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,20,7);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,21,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,22,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,23,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,24,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,25,5);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,26,6);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,27,7);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,28,8);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,29,9);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,30,10);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,31,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,32,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,33,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,34,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,35,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,36,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,37,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,38,4);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,39,5);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,40,6);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,41,7);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,42,8);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,43,9);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,44,1);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,45,2);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,46,3);
      INSERT INTO `complaint_subtypes_order` (`client_id`,`complaint_subtype_id`,`order`) VALUES (2,47,4);


?>