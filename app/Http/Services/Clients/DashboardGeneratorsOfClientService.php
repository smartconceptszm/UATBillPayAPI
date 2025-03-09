<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\DB;
use Exception;

class DashboardGeneratorsOfClientService
{

   public function findAll(string $client_id):array|null
   {
      
      try {

         $dashboardSnippets = DB::table('dashboard_snippets as ds')
                                 ->join('client_dashboard_snippets as cds','cds.dashboard_snippet_id','=','ds.id')
                                 ->join('client_dashboards as cd','cds.dashboard_id','=','cd.id')
                                 ->select('ds.generateHandler')
                                 ->distinct()
                                 ->where('cd.client_id', '=', $client_id)
                                 ->whereNotNull('ds.generateHandler')
                                 ->where('ds.generateHandler', '<>', '')
                                 ->get();

         $generators = [];
         if($dashboardSnippets){
            foreach ($dashboardSnippets as $record) {
               $generators[]=$record->generateHandler;
            }
         }

         return $generators;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}
