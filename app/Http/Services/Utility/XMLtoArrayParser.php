<?php
namespace App\Http\Services\Utility;

use Exception;

class XMLtoArrayParser 
{
    
	public  function handle(string $xmlString):array
	{
		try {
			$xmlData=$this->replaceSpecialChars($xmlString);
			$xml = \simplexml_load_string($xmlData, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = \json_encode($xml);
			return \json_decode($json,TRUE);
		} catch (\Throwable $e) {
			throw new Exception("XML Parsing error: ".$e->getMessage());
		}
	}

	private function replaceSpecialChars(String $xmlString): string
	{
		return preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xmlString);
	}

}
