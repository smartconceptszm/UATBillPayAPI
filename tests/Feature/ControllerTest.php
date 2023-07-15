<?php

use App\Http\Controllers\ComplaintsController;
use Tests\TestCase;

class ControllerTest extends TestCase
{

    public function _testTheMethod()
    {   

        $response = false;
        $theController = new ComplaintsController();
        $response = $theController->index();
        $this->assertTrue($response==true);

    }



}