<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\External\BillingClients\BillingClientBinderService;
use App\Http\BillPay\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\BillPay\Services\MoMo\ReConfirmMoMoPayment;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\BillPay\Repositories\PaymentToReviewRepo;
use Exception;

class PaymentNotConfirmedService implements IUpdateService
{

    private $momoClientBinderService;
    private $reConfirmMoMoPayment;
    private $billingClientBinder;
    private $repository;
    public function __construct(PaymentToReviewRepo $repository, 
        MoMoClientBinderService $momoClientBinderService,
        BillingClientBinderService $billingClientBinder,
        ReConfirmMoMoPayment $reConfirmMoMoPayment
    ){
        $this->momoClientBinderService=$momoClientBinderService;
        $this->reConfirmMoMoPayment=$reConfirmMoMoPayment;
        $this->billingClientBinder=$billingClientBinder;
        $this->repository = $repository;
    }

    public function update(array $data, string $id):object{
        try {
            $momoDTO=$this->repository->findById($id);
            //Bind the Services
                $this->billingClientBinder->billingClient($momoDTO);
                //Bind the MoMoClient
                $this->billingClientBinder->momoClient($momoDTO);
            //
            $momoDTO= $this->reConfirmMoMoPayment->handle($momoDTO);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        return $momoDTO;
    }

}
