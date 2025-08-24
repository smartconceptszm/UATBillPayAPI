<?php

namespace App\Http\Services\Promotions;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\PromotionEntry;

use Exception;

class PromotionEntryService
{

   public function __construct(
      private PromotionEntry $model
   ) {}

   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function entriesOfPromotion(?array $criteria):array|null
   {
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('promotion_entries as p')
                        ->select('p.*');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->where('p.entryDate', '>=', $dto->dateFrom)
                              ->where('p.entryDate', '<=',$dto->dateTo);
         }
         $records = $records->where('p.status', '=', 'RECORDED')
                              ->where('p.promotion_id', '=', $dto->promotion_id)
                              ->orderByDesc('p.entryDate')->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function redeemedEntriesOfPromotion(?array $criteria):array|null
   {
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('promotion_entries as p')
                        ->select('p.*');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->where('p.entryDate', '>=', $dto->dateFrom)
                              ->where('p.entryDate', '<=',$dto->dateTo);
         }
         $records = $records->where('p.status', '=', 'REDEEMED')
                              ->where('p.promotion_id', '=', $dto->promotion_id)
                              ->orderByDesc('p.entryDate')->get();
         return $records->all();
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


