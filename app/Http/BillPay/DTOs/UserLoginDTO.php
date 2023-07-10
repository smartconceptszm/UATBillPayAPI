<?php

namespace App\Http\BillPay\DTOs;

use App\Http\BillPay\DTOs\BaseDTO;
use JsonSerializable;

class UserLoginDTO extends BaseDTO implements JsonSerializable
{
   
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
   public $validationRules=[
      'username' => 'required|string',
      'password' => 'required|string',
   ];

   public function jsonSerialize(){
      return [
         'expires_in' => $this->expires_in,
         'token_type' => $this->token_type,
         'urlPrefix' => $this->urlPrefix,
         'client_id' => $this->client_id,
         'fullnames' => $this->fullnames,
         'username' => $this->username,
         'client' => $this->client,
         'rights' => $this->rights,
         'token' => $this->token,
         'id' => $this->id
      ];
   }

   public function credentials(){
      return [
         'username' => $this->username,
         'password' => $this->password
      ];
   }

}
