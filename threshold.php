<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 03/08/2018
 * Time: 13:06
 */

//1533301379

$msisdn = '22573485669';
$numero = substr_replace($msisdn, "", 0, 3);
$ucip_url = 'http://10.184.38.10:10010/Air';
$transacID = '1533301379';

$montant = 1;

$value = (($montant));
$methodName = 'UpdateUsageThresholdsAndCounters';


$xml_body = '

<methodCall>
<methodName>'.$methodName.'</methodName>
    <params>
        <param>
        <value>
                <struct>
                    <member><name>originNodeType</name><value><string>EXT</string></value></member>
                    <member><name>originHostName</name><value><string>gbichsva</string></value></member>
                    <member><name>originTransactionID</name><value><string>'.$transacID.'</string></value></member>
                    <member><name>originTimeStamp</name><value><dateTime.iso8601>' . date('Ymd') . 'T' . date('h:m:i') . '+0000</dateTime.iso8601></value></member>
                    <member><name>subscriberNumber</name><value><string>'.$numero.'</string></value></member>
                    <member><name>transactionCurrency</name><value><string>XOF</string></value></member>
                    <member><name>transactionType</name><value><string>gbichcosms</string></value></member>
                    <member><name>usageCounterUpdateInformation</name><value><array><data><value><struct><member><name>usageCounterID</name><value><int>7400</int></value></member>
                    <member><name>adjustmentUsageCounterMonetaryValueRelative</name><value><string>' . $value . '</string></value></member></struct></value></data></array></value></member></struct></value></param></params></methodCall>\';
                </struct>
            </value>
        </param>
    </params>
</methodCall>
';

$xml = '<?xml version="1.0"?>'.str_replace(' ', '', $xml_body);



$url = $ucip_url;

$header = array();
$header[] = 'Content-type: text/xml';
$header[] = 'Content-length: ' . strlen($xml);
$header[] = 'User-Agent: UGw Server/3.1/1.0';
$header[] = 'Authorization: Basic ' . base64_encode('gomedia:gomedia@2018');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ucip_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
$data = curl_exec($ch);

echo $data;