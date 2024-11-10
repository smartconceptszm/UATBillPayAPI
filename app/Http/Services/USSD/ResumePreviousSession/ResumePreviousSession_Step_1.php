<?php

namespace App\Http\Services\USSD\ResumePreviousSession;

use App\Http\DTOs\BaseDTO;

class ResumePreviousSession_Step_1
{

   public function run(BaseDTO $txDTO)
   {

      try {    

         $sessionToResume = (object)json_decode(cache($txDTO->sessionId."_Resume"),true);
         $txDTO->response .= $txDTO->menuPrompt."\n". 
                              "(".$sessionToResume->prompt.")\n".
                              "1. Yes\n".
                              "2. Start a new session";
         
      } catch (\Throwable $e) {
         $txDTO->error = 'Resume previous session step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }

      return $txDTO;
      
   }
   
}