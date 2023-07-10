<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
      DB::statement(" CREATE VIEW client_complaint_type_view 
         AS
         SELECT
            `cct`.`id` AS `id`,
            `cct`.`client_id` AS `client_id`,
            `cct`.`complaint_type_id` AS `complaint_type_id`,
            `cct`.`order` AS `order`,
            `ct`.`code` AS `code`,
            `ct`.`name` AS `name`
         FROM ( `client_complaint_types` `cct`
                  JOIN `complaint_types` `ct`
                     ON((`ct`.`id` = `cct`.`complaint_type_id`))
               );
      ");

      DB::statement(" CREATE VIEW client_complaint_subtype_view 
            AS
            SELECT
               `CCST`.`id` AS `id`,
               `CCST`.`order` AS `order`,
               `CCST`.`complaint_subtype_id` AS `complaint_subtype_id`,
               `CCST`.`client_id` AS `client_id`,
               `CST`.`code` AS `code`,
               `CST`.`name` AS `name`,
               `CST`.`requiresDetails` AS `requiresDetails`,
               `CST`.`detailType` AS `detailType`,
               `CST`.`prompt` AS `prompt`,
               `CST`.`complaint_type_id` AS `complaint_type_id`
            FROM `client_complaint_subtypes` `CCST`
               JOIN `complaint_subtypes` `CST` 
                     ON `CCST`.`complaint_subtype_id` = `CST`.`id`;
      "); 

      DB::statement(" CREATE VIEW client_otherpayment_type_view 
         AS
         SELECT
         `cpt`.`id` AS `id`,
         `cpt`.`client_id` AS `client_id`,
         `cpt`.`payment_type_id` AS `payment_type_id`,
         `cpt`.`ledgerAccountNumber` AS `ledgerAccountNumber`,
         `cpt`.`order` AS `order`,
         `cpt`.`prompt` AS `prompt`,
         `pt`.`code` AS `code`,
         `pt`.`name` AS `name`,
         `pt`.`receiptAccount` AS `receiptAccount`,
         `pt`.`hasApplicationNo` AS `hasApplicationNo`
         FROM ( `client_otherpayment_types` `cpt`
                  JOIN `otherpayment_types` `pt`
                     ON((`pt`.`id` = `cpt`.`payment_type_id`))
               );
      ");

      DB::statement(" CREATE VIEW client_customer_detail_view 
         AS
         SELECT
         `ccd`.`id` AS `id`,
         `ccd`.`client_id` AS `client_id`,
         `ccd`.`customer_detail_id` AS `customer_detail_id`,
         `ccd`.`order` AS `order`,
         `ccd`.`prompt` AS `prompt`,
         `cd`.`name` AS `name`,
         `cd`.`type` AS `type`,
         `cd`.`format` AS `format`
         FROM ( `client_customer_details` `ccd`
                  JOIN `customer_details` `cd`
                     ON((`cd`.`id` = `ccd`.`customer_detail_id`))
               );
      ");

    }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {

      Schema::dropIfExists('client_complaint_type_view');
      Schema::dropIfExists('client_complaint_subtype_view');
      Schema::dropIfExists('client_otherpayment_type_view');
      Schema::dropIfExists('client_customer_detail_view');
      
   }
};
