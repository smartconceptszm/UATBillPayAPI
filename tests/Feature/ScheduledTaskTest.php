<?php

use App\Http\ScheduledTasks\RetryFailedTrasactions;
use Tests\TestCase;

class ScheduledTaskTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testRunTask()
    {   

        $theTask = new RetryFailedTrasactions();
        $response=$theTask();
        
        $this->assertTrue($response == "Error on collect funds.");

    }

}