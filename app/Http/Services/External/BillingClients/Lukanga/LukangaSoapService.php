<?php

namespace App\Http\Services\External\BillingClients\Lukanga;

class LukangaSoapService extends \SoapClient
{

    function __doRequest($request, $location, $action, $version, $one_way = 0):?string {

        //$SoapServerUSERPWD = "";
        $SoapServerTimeout = \env('LUKANGA_SOAP_EXECUTE_TIMEOUT');

        $headers = array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "'.$action.'"',
        );
        
        $this->__last_request_headers = $headers;
        $ch= curl_init(\env('LUKANGA_BASE_URL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NEGOTIATE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_USERPWD, $SoapServerUSERPWD);
        curl_setopt($ch, CURLOPT_VERBOSE,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $SoapServerTimeout);

        $response = curl_exec($ch);
        return $response;

    }

    function __getLastRequestHeaders():?string {
        return implode("\n", $this->__last_request_headers)."\n";
    }

}