<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;

class MenuBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IUSSDMenu::class,App::make($key));
   }

}