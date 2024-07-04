<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;

class DummyMenu implements IUSSDMenu
{
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {
      //To handle error at the Steps Before HandleMenu.
      return $txDTO;
      
   }
    
}
