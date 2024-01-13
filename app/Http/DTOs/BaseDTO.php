<?php

namespace App\Http\DTOs;

abstract class BaseDTO 
{
   
   public $id;
   public $exitPipeline = false;
   public $validationRules;
   
   public function toArray(): array
   {

      $arrValues=\get_object_vars($this);
      unset($arrValues['validationRules']);
      return $arrValues;
      
   }

   public function fromArray(array $values ): BaseDTO
   {
      foreach ($values as $key => $value) {
         if (\property_exists($this, $key)) {
               $this->$key = $value;
         }
      }
      return $this;
   }

}