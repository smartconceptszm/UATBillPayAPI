<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class USSDMenuHandlerTest extends TestCase
{

   public function _test_menu(): void
   {
      
      $menu = new \App\Http\Services\USSD\Menus\ResumePreviousSession();
      $response = $menu->handle(new \App\Http\DTOs\UssdDTO());
      $this->assertTrue($response);
   }


}
