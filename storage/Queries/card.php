Step 1 getToken,                                                                                                                                                                      
POST , https://secure.3gdirectpay.com/API/v6/                                                                                                                         
<?xml version=\"1.0\" encoding=\"utf-8\"?>
<API3G>
    <CompanyToken>B3F59BE7-0756-420E-BB88-1D98E7A6B040</CompanyToken>
    <Request>createToken</Request>
    <Transaction>
        <PaymentAmount>10.00</PaymentAmount>
        <PaymentCurrency>USD</PaymentCurrency>
        <CompanyRef>49FKEOA</CompanyRef>
        <RedirectURL>http://www.domain.com/payurl.php</RedirectURL>
        <BackURL>http://www.domain.com/backurl.php </BackURL>
        <CompanyRefUnique>0</CompanyRefUnique>
        <PTL>5</PTL>
    </Transaction>
    <Services>
        <Service>
            <ServiceType>54841</ServiceType>
            <ServiceDescription>Test Product</ServiceDescription>
            <ServiceDate>2024/04/29 19:27</ServiceDate>
        </Service>
    </Services>
</API3G>                                                                                                                                                                                               
RESPONSE            
<?xml version="1.0" encoding="utf-8"?><API3G><Result>000</Result><ResultExplanation>Transaction created</ResultExplanation><TransToken>98F40E7D-6771-44BA-B29D-BF11CADC2E90</TransToken><TransRef>R57159088</TransRef></API3G>



Step 2 Charge Card,            POST      , <?xml version="1.0" encoding="utf-8"?>
<API3G>
  <CompanyToken>B3F59BE7-0756-420E-BB88-1D98E7A6B040</CompanyToken>
  <Request>chargeTokenCreditCard</Request>
  <TransactionToken>B32C68B4-544B-4A21-AABC-46B82B3A3533</TransactionToken>
  <CreditCardNumber>5436886269848367</CreditCardNumber>
  <CreditCardExpiry>1224</CreditCardExpiry>
  <CreditCardCVV>123</CreditCardCVV>
  <CardHolderName>John Doe</CardHolderName>
  <ChargeType></ChargeType>
  
</API3G>.



Step 3 Verfit Token , POST , Same URL, <?xml version="1.0" encoding="utf-8"?>
<API3G>
  <CompanyToken>B3F59BE7-0756-420E-BB88-1D98E7A6B040</CompanyToken>
  <Request>verifyToken</Request>
  <TransactionToken>B32C68B4-544B-4A21-AABC-46B82B3A3533</TransactionToken>
</API3G>