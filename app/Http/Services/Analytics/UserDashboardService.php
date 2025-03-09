<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Clients\ClientDashboardSnippetService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use Exception;

class UserDashboardService 
{

   public function __construct(
      private ClientDashboardSnippetService $clientDashboardSnippetService,
      ) 
   {}

   public function findAll(array $criteria):array|null
   {
   
      try {
         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom)->startOfDay();
         $dateTo = Carbon::parse($dto->dateTo)->endOfDay();

         if($dateTo < $dateFrom){
            $dateTo = $dateFrom;
         }

         $dateFromYMD = $dateFrom->copy()->format('Y-m-d H:i:s');
         $dateToYMD = $dateTo->copy()->format('Y-m-d H:i:s');

         $snippets = $this->clientDashboardSnippetService->dashboardSnippetsOfClient($dto->client_id,'Revenue Collector Dashbaord');
         $params = [
                     'revenueCollectorCode'=>$dto->revenueCollectorCode,
                     'client_id' => $dto->client_id,
                     'dateFromYMD' => $dateFromYMD,
                     'dateToYMD' => $dateToYMD,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                     'id' => $dto->client_id,
                  ];

         $response = [];
      
         foreach ($snippets as $snippet) {
            $snippetHandler = App::make($snippet->viewHandler);
            $data = $snippetHandler->findAll($params);
            $data['sizeOnPage'] = $snippet->sizeOnPage;
            $data['hyperlink'] = $snippet->hyperlink;
            $data['title'] = $snippet->title;
            $data['type'] = $snippet->type;
            $data['id'] = $snippet->id;
            $response[$snippet->rowNumber][$snippet->columnNumber] = $data;
         }

         return $response;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
