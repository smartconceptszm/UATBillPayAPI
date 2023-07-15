<?php

namespace App\Http\BillPay\Services\CRM;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\CRM\SurveyEntryRepo;

class SurveyEntryService extends BaseService
{

   public function __construct(SurveyEntryRepo $repository)
   {
      parent::__construct($repository);
   }

   public function create(array $data):object|null
   {

      try {
         $data['caseNumber'] = $data['mobileNumber'].'_'.date('YmdHis');
         $response = $this->repository->create($data);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
      
   }

}
