<?php

namespace Tests\Feature;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use Tests\TestCase;

/**
 * Real end-to-end smoke test -- runs against the actual MySQL
 * `billpay_production` database (see .env DB_DATABASE), using the real
 * "mulonga" client and its real menu configuration. No RefreshDatabase/
 * DatabaseTransactions: this persists real rows (ussd_sessions, and an
 * sms_messages row once the queued notification job runs) on purpose, for
 * manual inspection afterwards. Clean those up yourself when done.
 *
 * IMPORTANT: as of this writing, client_menus.handler = 'RateUs' (order 5,
 * under mulonga's main menu) has isActive = 'NO' in production. ParentMenu
 * only lists isActive = 'YES' siblings, and RetrieveCurrentMenu throws
 * "Invalid Menu Item number" for an inactive one, so picking option 5 will
 * fail unless it's turned on first. ensureRateUsMenuIsActive() flips it to
 * 'YES' before the walk runs and leaves it that way afterward -- flip it
 * back yourself if you don't want "Rate Us" left live for real subscribers.
 *
 * Method names are prefixed with an underscore so `php artisan test` does
 * NOT pick them up automatically (PHPUnit only runs methods matching
 * test*) -- run one explicitly with:
 *   php artisan test --filter=_test_mtn
 */
class RateUsJourneyTest extends TestCase
{
    private const URL_PREFIX = 'mulonga';
    // private const MSISDN = '260972702707';
    private const MSISDN = '260965199175';
    private const RATE_US_MENU_ORDER = '5';

    private function ensureRateUsMenuIsActive(): void
    {
        $client = app(ClientService::class)->findOneBy(['urlPrefix' => self::URL_PREFIX]);

        $clientMenuService = app(ClientMenuService::class);
        $rateUsMenu = $clientMenuService->findOneBy([
            'client_id' => $client->id,
            'handler' => 'RateUs',
        ]);

        if ($rateUsMenu && $rateUsMenu->isActive !== 'YES') {
            $clientMenuService->update(['isActive' => 'YES'], $rateUsMenu->id);
        }
    }

    public function test_airtel(): void
    {
        $this->ensureRateUsMenuIsActive();

        $sessionId = uniqid('sess-rateus-airtel-');

        // Screen 1: dial -> main menu
        $response = $this->get('/'.self::URL_PREFIX.'/airtel?MSISDN='.self::MSISDN.'&SUBSCRIBER_INPUT=&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=1');
        $responseHuman = $response->getContent();
        $response->assertStatus(200);

        // Pick "5. Rate Us" -> RateUs::handle() runs: queues the SMS and ends the session
        $response = $this->get('/'.self::URL_PREFIX.'/airtel?MSISDN='.self::MSISDN.'&SUBSCRIBER_INPUT='.self::RATE_US_MENU_ORDER.'&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
        $responseHuman = $response->getContent();
        $response->assertStatus(200);
        $this->assertStringContainsString('Please check your SMS', $responseHuman);
    }

    /**
     * MTN sends lowercase/camelCase params -- msisdn/subscriberInput/sessionId/isnewrequest
     * instead of Airtel's ALL_CAPS convention.
     */
    public function _test_mtn(): void
    {
        $this->ensureRateUsMenuIsActive();

        $sessionId = uniqid('sess-rateus-mtn-');

        $response = $this->get('/'.self::URL_PREFIX.'/mtn?msisdn='.self::MSISDN.'&subscriberInput=&sessionId='.$sessionId.'&isnewrequest=1');
        $response->assertStatus(200);

        $response = $this->get('/'.self::URL_PREFIX.'/mtn?msisdn='.self::MSISDN.'&subscriberInput='.self::RATE_US_MENU_ORDER.'&sessionId='.$sessionId.'&isnewrequest=0');
        $response->assertStatus(200);
        $this->assertStringContainsString('Please check your SMS', $response->getContent());
    }

    /**
     * Zamtel sends the whole dialed string as USSDString (e.g. "*2012*5")
     * plus a RequestType flag, instead of separate subscriberInput/
     * isNewRequest fields -- see USSDZamtelController::parseGatewayParams().
     */
    public function _test_zamtel(): void
    {
        $this->ensureRateUsMenuIsActive();

        $transId = uniqid('sess-rateus-zamtel-');

        // New session: USSDString is the raw dial string, wrapped in */#
        $response = $this->get('/'.self::URL_PREFIX.'/Zamtel?&TransId='.$transId.'&Pid=0&RequestType=1&MSISDN='.self::MSISDN.'&SHORTCODE=2012&cellID=000000000000000&AppId=1&USSDString=*2012%23');
        $response->assertStatus(200);

        // Continuing: USSDString accumulates every input so far, no trailing "#"
        $response = $this->get('/'.self::URL_PREFIX.'/Zamtel?&TransId='.$transId.'&Pid=0&RequestType=0&MSISDN='.self::MSISDN.'&SHORTCODE=2012&cellID=000000000000000&AppId=1&USSDString=*2012*'.self::RATE_US_MENU_ORDER);
        $response->assertStatus(200);
        $this->assertStringContainsString('Please check your SMS', $response->getContent());
    }
}
