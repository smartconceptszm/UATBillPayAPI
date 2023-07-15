<?php

namespace App\Http\BillPay\Services\CRM;

use App\Http\BillPay\Repositories\CRM\ServiceApplicationRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ServiceApplicationService extends BaseService
{

   public function __construct(ServiceApplicationRepo $repository)
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
