<?php


use App\Http\BillPay\Repositories\ClientComplaintSubTypeViewRepo;
use Tests\TestCase;

class BillPayRepoTest extends TestCase
{

    public function _testSearchBy()
    {   

        $criteria = ['order'=>'0', 'client_id'=>'2', 'complaint_subtype_id'=>'1'];
        $theRepo = new ClientComplaintSubTypeViewRepo();
        $response = $theRepo->findOneBy($criteria);
        $response = json_encode($response);
        $this->assertTrue($response==true);

    }

}