<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientWallet extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "client_wallets";

   protected $fillable=[
      'client_id','payments_provider_id','handler','paymentMethod','paymentsActive',
      'paymentsCommission','paymentsMode','modeMessage'
   ];
   
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
