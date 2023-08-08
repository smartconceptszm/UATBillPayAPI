<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class Authorise
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle($request, Closure $next, $right)
   {
      $user = Auth::user();   
      if (\in_array($right, $user->rights)) {
         return $next($request);
      }
      return $this->unauthorized();
   }

   private function unauthorized($message = null){
      return response()->json([
         'message' => $message ? $message : 'User not unauthorized to access this resource',
      ], 403);
   }

}
