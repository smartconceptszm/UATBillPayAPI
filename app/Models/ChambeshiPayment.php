<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChambeshiPayment extends Model
{
   use HasFactory;

   protected $connection;
   protected $table = "SmartConcept";
   protected $fillable=[
      'TxDate','Account', 'AccountName','Debt','AmountPaid','Phone#',
      'ReceiptNo','Address','District','TransactDescript','Mobile Network'
   ];

   protected $casts = [
      'TxDate' => 'datetime:Y-m-d H:i:s'
   ];

   public $timestamps = false; 

   public function __construct() {
      $this->connection = \env('CHAMBESHI_DB_CONNECTION');
   }
      
}
