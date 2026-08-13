<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Services\USSD\USSDService;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\UssdDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WhatsAppController extends Controller
{

   public function __construct(
      protected USSDService $ussdService,
      protected SessionService $sessionService,
      protected MnoService $mnoService,
      protected UssdDTO $ussdDTO)
   {}

   public function index(Request $request)
   {

      try {
         //Parse RequestParameters
         //POST /kafubu/whatsapp {"subscriberInput":"1","mobileNumber":"260977787659"}
         $requestParams = $request->all();

         $whatsAppParams['mobileNumber'] = $this->normaliseMobileNumber((string) ($requestParams['mobileNumber'] ?? ''));
         $whatsAppParams['subscriberInput'] = \trim((string) ($requestParams['subscriberInput'] ?? ''));
         //The chatbot's own request tells us which client (nkana, kafubu, ...) this is for -
         //there's no per-client URL to derive it from, unlike the USSD channels.
         $whatsAppParams['urlPrefix'] = \trim((string) ($requestParams['urlPrefix'] ?? ''));

         //No telecom gateway to tell us the MNO either, so derive it from the MSISDN prefix,
         //same as SMSService does for the same reason.
         $whatsAppParams['mnoName'] = MNOs::getMNO(\substr($whatsAppParams['mobileNumber'], 0, 5));
         $mno = $this->mnoService->findOneBy(['name' => $whatsAppParams['mnoName']]);
         $whatsAppParams['mno_id'] = $mno->id;
         $whatsAppParams['payments_provider_id'] = $mno->payments_provider_id;

         //No telecom to hand us a session id, so we track continuity by mobileNumber instead.
         //sessionId has to stay the SAME value while a conversation is in progress -
         //RetrieveSession looks the row up by mobileNumber+sessionId together - but a brand
         //new conversation needs a sessionId that's never been used before: the sessions
         //table has a unique constraint on (sessionId, mobileNumber), and reusing the bare
         //mobileNumber forever means a customer's second-ever conversation collides with the
         //row from their first and fails to save.
         $lastSession = $this->sessionService->findLatestBy(['mobileNumber' => $whatsAppParams['mobileNumber']]);
         $whatsAppParams['isNewRequest'] = $this->isNewSession($lastSession) ? '1' : '0';
         $whatsAppParams['sessionId'] = $whatsAppParams['isNewRequest'] === '1'
            ? $whatsAppParams['mobileNumber'].'-'.\now()->format('YmdHisv')
            : $lastSession->sessionId;

         $this->ussdDTO = $this->ussdDTO->fromArray($whatsAppParams);
         //Process the Request
         $this->ussdDTO = $this->ussdService->handle($this->ussdDTO);

      } catch (\Throwable $e) {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $this->ussdDTO->error = 'Error: At WhatsApp controller level. '.$e->getMessage();
         $this->ussdDTO->response = $billpaySettings['ERROR_MESSAGE'] ?? 'Service temporarily unavailable, please try again later.';
         $this->ussdDTO->lastResponse = true;
      }
      return $this->responder();

   }

   private function normaliseMobileNumber(string $mobileNumber): string
   {
      return \ltrim(\trim($mobileNumber), '+');
   }

   /**
    * WhatsApp has no telecom-managed session, so we infer "new vs continuing" from the
    * sessions table: no row, or one older than SESSION_CACHE minutes, means new.
    * A message sent moments after a just-completed conversation will still read
    * as "continuing" under this heuristic - the same limitation any purely
    * time-based inference has without a telecom session boundary to anchor to.
    */
   private function isNewSession(?object $lastSession): bool
   {

      if (!$lastSession) {
         return true;
      }

      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $sessionCacheMinutes = \intval($billpaySettings['SESSION_CACHE'] ?? 5);

      //sessions.updated_at round-trips through MySQL as a UTC-equivalent value, not
      //APP_TIMEZONE (Africa/Lusaka) - parsing it without saying so makes every session
      //look ~2 hours older than it is, which is enough to blow past SESSION_CACHE on
      //every single request regardless of how fast the customer actually replies.
      return Carbon::parse($lastSession->updated_at, 'UTC')->addMinutes($sessionCacheMinutes)->isPast();

   }

   private function responder()
   {
      return response()->json([
         'response' => $this->ussdDTO->response,
         'lastResponse' => (bool) $this->ussdDTO->lastResponse,
      ]);
   }

}
