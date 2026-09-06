<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class APIClient extends Authenticatable implements JWTSubject
{

   use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = "api_clients";

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
         'category','service_provider_id','payments_provider_id','username','password','shortName',
         'name','mobileNumber','email','channel','status'
      ];

   /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
   protected $hidden = [
         'password'
      ];


   protected $attributes = [
         'category' => 'OTHER',
         'status' => 'REGISTERED'
      ];

   /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
         'password' => 'hashed',
      ];

   /**
    * Get the identifier that will be stored in the subject claim of the JWT.
    *
    * @return mixed
    */
   public function getJWTIdentifier()
   {
      return $this->getKey();
   }

   /**
    * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @return array
    */
   public function getJWTCustomClaims()
   {
      return [];
   }

}
