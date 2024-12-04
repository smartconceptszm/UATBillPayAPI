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
      ];
   }

   public function credentials(){
      return [
         'username' => $this->username,
         'password' => $this->password
      ];
   }

}
