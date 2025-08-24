//1. SMS Dashboard Feature
	Update the 'SMS Providers Table'
	1. insert new column 'payments_provider_id'
	2. insert new SMS Provider 'MTNMoMoSMS' linked to MTN payments provider
	2. Update Handler Name for 'MTNSMS' to 'CPASSSMS'

	Update the 'Messages Table'
	1. insert new column 'channel'

		UPDATE `messages` m
		JOIN `client_mnos` cmno ON m.client_id = cmno.client_id 
							AND m.mno_id = cmno.mno_id
		JOIN `client_sms_channels` csmsc ON cmno.smsChannel = csmsc.id 
		JOIN `sms_providers` smsp ON smsp.id = csmsc.sms_provider_id
		SET m.channel = smsp.handler;

	Create the 'SMS Dashboard Tables'
	1. sms_dashboard_channel_totals
	2. sms_dashboard_type_totals

	Update the SMS dashboard tables 
	1. run queries over each of the months in 2025

//2. BANK API

	Update the 'payments  Table'
	1. insert new column 'customerJourney' nullable
	2. Update the field 'session_id' to nullable
	3. Update the enum field 'channel' by adding the values "BANK-FULL", "BANK-RECEIPTING" to replace "BANK-API"
	4. Update the field 'mobileNumber' to nullable

	Update 'Payments Provider Table'
	1. insert new column 'client_id' nullable - to link to bank in clients table

//3. Composite Accounts
	Update the 'client_customers Table'
	1. insert new column 'composite' enum ['PARENT','CHILD','ORDINARY']
	2. insert new column 'parent_id' int nullable	
	3. insert new column 'customerName' varchar nullable
	4. insert new column 'balance' double default 0
	6. Change default value for the following fields to null
			revenuePoints
			consumerType
			consumerTier
	5. Update the Kafubu accounts 
		.Insert the 138 parent records
		.update the children


		INSERT INTO client_customers (`client_id`,`customerAccount`,`composite`,`customerName`)
		SELECT `client_id`,`customerAccount`,`composite`,`customerHolder` as `customerName` 
		FROM client_customers_temp
		WHERE composite = 'PARENT';


		INSERT INTO client_customers (`client_id`,`customerAccount`,`composite`,`customerName`)
		SELECT cct.`client_id`, cct.customerAccount,cct.composite,cct.customerHolder as `customerName`
		FROM client_customers_temp cct
		LEFT JOIN client_customers cc ON cct.customerAccount = cc.customerAccount
		WHERE cc.customerAccount IS NULL;

		//For checking purposes
			SELECT cc.id, cc.customerAccount,cc.composite, cc2.id as `parent_id`
			FROM `client_customers` as cc
			JOIN `temp_Composites` as tc ON tc.account = cc.customerAccount
			JOIN `client_customers` as cc2 ON tc.employer = cc2.customerAccount
			WHERE cc.client_id = 'a1ca6f8c-240b-11ef-98b6-0a3595084709'
			ORDER BY cc2.id;
		
			//LINK CHILDREN TO PARENTS
			UPDATE`client_customers` as cc

			JOIN `temp_Composites` as tc ON tc.account = cc.customerAccount
			JOIN `client_customers` as cc2 ON tc.employer = cc2.customerAccount

			SET cc.parent_id = cc2.id, cc.composite = 'CHILD'

			WHERE cc.client_id = 'a1ca6f8c-240b-11ef-98b6-0a3595084709';

//4. Payments Audit Table
	1. create new table payments_audit

//4. Promotions 