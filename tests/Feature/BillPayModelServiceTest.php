<?php

use App\Http\BillPay\Repositories\ClientComplaintTypeViewRepo;
use App\Http\BillPay\Services\ClientComplaintTypeViewService;
use App\Http\BillPay\DTOs\ClientComplaintTypeViewDTO;
use App\Models\Client_Complaint_Type_View;
use Tests\TestCase;

class BillPayModelServiceTest extends TestCase
{

    public function _testService()
    {   


        $service = new ClientComplaintTypeViewService(
                        new ClientComplaintTypeViewRepo( 
                            new Client_Complaint_Type_View()
                        )
                    );
        $dto = new ClientComplaintTypeViewDTO();
        $dto->client_id=2;
        $arrData = $service->findAll();
        $this->assertTrue(count($arrData) == 8);

    }

}