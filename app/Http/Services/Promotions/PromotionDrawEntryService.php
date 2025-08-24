<?php

namespace App\Http\Services\Promotions;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\PromotionDrawEntry;
use Illuminate\Support\Carbon;
use Exception;

class PromotionDrawEntryService
{

   public function __construct(
      private PromotionDrawEntry $model
   ) {}

   public function findAll(?array $criteria):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function drawEntriesOfPromotion(?array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('promotion_draw_entries as p')
                        ->select('p.*');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->where('p.entryDate', '>=', $dto->dateFrom)
                              ->where('p.entryDate', '<=',$dto->dateTo);
         }
         $records = $records->where('p.promotion_id', '=', $dto->promotion_id)
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

   public function drawRandom(array $params) : object|null {

        try {
            if(isset($params['theMonth'])){

               $theDate = Carbon::createFromFormat('Y-m-d',$params['theMonth'].'-01');
               $startOfMonth = $theDate->copy()->startOfMonth();
               $endOfMonth = $theDate->copy()->endOfMonth();

               $item = $this->model::whereNull('drawNumber')
                                    ->where('promotion_id',$params['promotion_id'])
                                    ->whereBetween('entryDate', [$startOfMonth, $endOfMonth])
                                    ->inRandomOrder()
                                    ->first();
            }else{
               $item = $this->model::whereNull('drawNumber')
                                    ->whereBetween('entryDate', [$params['from'], $params['to']])
                                    ->where('promotion_id',$params['promotion_id'])
                                    ->inRandomOrder()
                                    ->first();
            }

            $item = \is_null($item)?null:(object)$item->toArray();

            //Flag Raffle Winner with YES
            // $this->update(["raffleWinner"=>"YES"],$item->id) ? $item->raffleWinner='YES':$item;

            return $item;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
            // Output to Debugbar
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