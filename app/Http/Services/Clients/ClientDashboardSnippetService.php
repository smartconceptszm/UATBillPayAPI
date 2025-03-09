<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\Schema;
use App\Models\ClientDashboardSnippet;
use Illuminate\Support\Facades\DB;
use Exception;

class ClientDashboardSnippetService
{

   public function __construct(
         private ClientDashboardSnippet $model
   ) {}

   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function snippetsOfDashboard(string $dashboard_id)
   {
      
      try {

         $dashboardSnippets = DB::table('client_dashboard_snippets as cds')
                                 ->join('dashboard_snippets as ds','cds.dashboard_snippet_id','=','ds.id')
                                 ->join('client_dashboards as cd','cds.dashboard_id','=','cd.id')
                                 ->join('clients as c','c.id','=','cd.client_id')
                                 ->select('cds.*','c.shortName','c.name as client','cd.name as dashboard','ds.name as snippet',
                                          'ds.type','ds.title','ds.generateHandler','cds.viewHandler')
                                 ->where('cds.dashboard_id', '=', $dashboard_id)
                                 ->orderBy('cds.rowNumber')
                                 ->orderBy('cds.columnNumber')
                                 ->get();
         return $dashboardSnippets;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function dashboardSnippetsOfClient(string $client_id, string $dashboard)
   {
      
      try {

         $dashboardSnippets = DB::table('dashboard_snippets as ds')
                                 ->join('client_dashboard_snippets as cds','cds.dashboard_snippet_id','=','ds.id')
                                 ->join('client_dashboards as cd','cds.dashboard_id','=','cd.id')
                                 ->select('cds.id','cds.rowNumber','cds.columnNumber','cds.sizeOnPage','cds.viewHandler',
                                          'cds.hyperlink','ds.type','ds.title')
                                 ->where('cd.client_id', '=', $client_id)
                                 ->where('cd.name', '=', $dashboard)
                                 ->where('cds.isActive', '=', "YES")
                                 ->orderBy('cds.rowNumber')
                                 ->orderBy('cds.columnNumber')
                                 ->get();

         return $dashboardSnippets;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findById(string $id) : object|null {
      try {
         $item = $this->model->findOrFail($id);
         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         $item = $this->model->where($criteria)->first();
         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function create(array $data) : object|null {
      try {
         foreach ( $data as $key => $value) {
            if (Schema::hasColumn($this->model->getTable(), $key) && $value != '') {
               $this->model->$key = $value;
            }
         }
         $this->model->save();
         return $this->model;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function update(array $data, string $id) : object|null {

      try {
         unset($data['id']);
         $record = $this->model->findOrFail($id);
         foreach ($data as $key => $value) {
            $record->$key = $value;
         }
         if($record->isDirty()){
            $record->save();
         }
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id) : bool{
      try {
         return $this->model->where('id', $id)->delete();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
