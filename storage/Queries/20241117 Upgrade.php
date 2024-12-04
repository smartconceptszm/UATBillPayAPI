<?php 

//Tables with Changes

//1 Client Menu
   1. Update database table 'client_menus' - add paymentType column 
   2. Update values for the new field


//2 MNO SMS Credentials
   1. Create New table - sms_providers
   1. Create New table - client_sms_channels

   2. RENAME table -  "mno_sms_credentials" to "sms_provider_credentials"
   3. RENAME table -  "client_mno_credentials" to "sms_channels_credentials"

   4. RENAME Field client_mno -  "handler" to "smsChannel"

   2. Update values for Zamtel and MTNSMS SMS Providers
   3. Test Sending SMS with MTN

   Update Corresponding sms channel credentials for all clients

//3. Scheduled Task
   Test new implementation with Job


//4. Resume Menu Feature
   1. Add 'Resume' menu database entry in 'client_menu' tables - refer to local database for an example

//6. Enable MoMoCallBack for Chambeshi
   1. Add credentials to wallet credentials table


//7. Council Analytics Upgrade
   1. Create New tables:
               . client_revenue_points
               . dashboard_revenue_collector_totals
   
   2. Change Dashboard_district_totals - to dashboard_revenue_point_totals
      . change field name from 'district' to 'revenuePoint'

   3. payments Table
      . change field name from 'district' to 'revenuePoint' 
      . Add new field "revenueCollector" and index
      
   4. sessions Table
      . change field name from 'district' to 'revenuePoint' 
   5. customerdetail Table
      . change field name from 'district' to 'revenuePoint' 
   6. surveyentry Table
      . change field name from 'district' to 'revenuePoint' 

      Update user table - insert new column "revenenueCollectorCode"

//8. Enable Shortcuts fro Council Payments

   SELECT `id` FROM `client_menus` WHERE `client_id` = "39d62960-7303-11ee-b8ce-fec6e52a2330" 
               AND `handler` ='ParentMenu' AND `parent_id` = "8a2d728a-7306-11ee-b8ce-fec6e52a2330";

   UPDATE `client_menus` SET `shortcut` = "MakeCouncilPayment" 
   WHERE `id` IN ('8a2d73b6-7306-11ee-b8ce-fec6e52a2330','8a2d75e6-7306-11ee-b8ce-fec6e52a2330',
               '8a2d776c-7306-11ee-b8ce-fec6e52a2330','9cb14d04-e3c6-4548-b8d2-cbd22b0d9779',
               '9cb14f6a-6e89-4080-a283-ce3765786e50','9cb15056-477c-420f-9901-b88aac719c7e');


//9. Enable MoMoCallBack for Mazabuka
   1. Add credentials to wallet credentials table




   

//5. SWASCO Billing client v2
1. Add billing credentials for SOAP API 

