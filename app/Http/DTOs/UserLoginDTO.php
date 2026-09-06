<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;
use JsonSerializable;

class UserLoginDTO extends BaseDTO implements JsonSerializable
{

   public $revenueCollectorCode;
   public $expires_in;
   public $token_type;
   public $urlPrefix;
   public $client_id;
   public $fullnames;
   public $username;
   public $password;

   public $payments_provider_id;
   public $api_client_id;
   public $mobileNumber;
   public $shortName;
   public $status;
   public $email;
   public $name;

   public $client;
   public $rights;
   public $token;
   public $id;
   public $validationRules=[
      'username' => 'required|string',
      'password' => 'required|string',
   ];

   public function jsonSerialize():mixed{
      return [
         'id' => $this->id,
         'username' => $this->username,
         'fullnames' => $this->fullnames,
         'urlPrefix' => $this->urlPrefix,
         'client_id' => $this->client_id,
         'client' => $this->client,
         'expires_in' => $this->expires_in,
         'token_type' => $this->token_type,
         'token' => $this->token,
         'revenueCollectorCode' => $this->revenueCollectorCode,
         'rights' => $this->rights,

         'payments_provider_id' => $this->payments_provider_id,
         'api_client_id' => $this->api_client_id,
         'mobileNumber' => $this->mobileNumber,
         'shortName' => $this->shortName,
         'status' => $this->status,
         'email' => $this->email,
         'name' => $this->name

      ];
   }

   public function credentials(){
      return [
         'username' => $this->username,
         'password' => $this->password
      ];
   }

}
