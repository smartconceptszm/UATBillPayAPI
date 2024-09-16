$xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
                        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                            <soapenv:Body>
                                <api:Result xmlns:api="http://cps.huawei.com/synccpsinterface/api_requestmgr" xmlns:com="http://cps.huawei.com/synccpsinterface/common" xmlns:res="http://cps.huawei.com/synccpsinterface/result">
                                    <res:Header>
                                        <res:Version>1.0</res:Version>
                                        <res:OriginatorConversationID>7796a720-b642-48bf-a983-10532bcc34a9</res:OriginatorConversationID>
                                        <res:ConversationID>PRE_AG_20240915_101a6cfc17f3102f</res:ConversationID>
                                    </res:Header>
                                    <res:Body>
                                        <res:ResultType>0</res:ResultType>
                                        <res:ResultCode>0</res:ResultCode>
                                        <res:ResultDesc>Process service request successfully.</res:ResultDesc>
                                        <res:TransactionResult>
                                            <res:TransactionID>000007917005</res:TransactionID>
                                            <res:ResultParameters>
                                                <res:ResultParameter>
                                                    <com:Key>DebitBalance</com:Key>
                                                    <com:Value>{"list":[{"accountno":"100000000115661017","accounttypename":"Customer Dedicated Account","amount":"0.00","currency":"ZMW"},{"accountno":"100000000115661041","accounttypename":"Customer EMoney Account","amount":"963.12","currency":"ZMW"},{"accountno":"100000000115661025","accounttypename":"Customer Promotion Account","amount":"0.00","currency":"ZMW"},{"accountno":"100000000115661033","accounttypename":"Emoney Dedicated Customer Account","amount":"0.00","currency":"ZMW"}],"total":[{"amount":"963.12","currency":"ZMW"}]}</com:Value>
                                                </res:ResultParameter>
                                                <res:ResultParameter>
                                                    <com:Key>CreditBalance</com:Key>
                                                    <com:Value />
                                                </res:ResultParameter>
                                            </res:ResultParameters>
                                        </res:TransactionResult>
                                        <res:ReferenceData>
                                            <res:ReferenceItem>
                                                <com:Key>BillReferenceNumber</com:Key>
                                                <com:Value>82eb11df-49ac-46cf-b608-9ed448beb165</com:Value>
                                            </res:ReferenceItem>
                                        </res:ReferenceData>
                                    </res:Body>
                                </api:Result>
                            </soapenv:Body>
                        </soapenv:Envelope>';

        // $xmlObject = simplexml_load_string($xmlBody);
        // // $bodyContent = $xmlObject->xpath('//soapenv:Body')[0];
        // $jsonArray = json_decode(json_encode($xmlObject), true);

        $xmlObject = simplexml_load_string($xmlBody, 'SimpleXMLElement', LIBXML_NOCDATA);

        // Register the namespaces and fetch the data
        $namespaces = $xmlObject->getNamespaces(true);
        
        // Use namespace to find the nodes
        $body = $xmlObject->children($namespaces['soapenv'])->Body->children($namespaces['api'])->Result->children($namespaces['res'])->Body;
        
        // Convert the SimpleXMLElement object to JSON, then decode it into a PHP array
        $jsonArray = json_decode(json_encode($body), true);



