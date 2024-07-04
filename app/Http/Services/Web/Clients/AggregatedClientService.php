<?php

namespace App\Http\Services\Web\Clients;

use Illuminate\Support\Facades\DB;
use App\Models\AggregatedClient;
use Exception;

class AggregatedClientService
{

   public function __construct(
         protected AggregatedClient $model
   ) {}

   public function findAll(array $criteria = null):array|null
   {
      try {
         return $this->model->where($criteria)->orderByDesc('menuNo')->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function getClientsOfAggregator(string $parent_id):array|null
   {
      try {
         $records = DB::table('aggregated_clients as ag')
                  ->join('clients as c','ag.client_id','=','c.id')
                  ->select('ag.*','c.shortName','c.urlPrefix','c.mode', 'c.status','c.name')
                  ->where('ag.parent_id', '=', $parent_id);
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findById(string $id) : object|null {
      try {
         return $this->model->findOrFail($id);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         return $this->model->where($criteria)->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function create(array $data) : object|null {
      try {
         foreach ( $data as $key => $value) {
            if($value == ''){
                  unset($data[$key]);
            }
         }
        return $this->model->create($data);
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
