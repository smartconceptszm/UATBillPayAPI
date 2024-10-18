<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;

class PlaceHolderMenu implements IUSSDMenu
{

   public function __construct(
      private ClientMenuService $clientMenuService) 
   {}
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
      $txDTO->response = $clientMenu->prompt.' option is coming soon! Thank you for the your patience';
      $txDTO->lastResponse = true;
      return $txDTO;
      
   }
    
}
