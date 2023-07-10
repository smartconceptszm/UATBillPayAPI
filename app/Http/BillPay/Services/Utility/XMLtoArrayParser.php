<?php
namespace App\Http\BillPay\Services\Utility;

class XMLtoArrayParser 
{
    
    public  function handle(string $xmlString):array
    {
        $xmlData=$this->replaceSpecialChars($xmlString);
        $xml = \simplexml_load_string($xmlData, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = \json_encode($xml);
        return \json_decode($json,TRUE);
    }

    private function replaceSpecialChars(String $xmlString): string
    {
        return preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xmlString);
    }

}
