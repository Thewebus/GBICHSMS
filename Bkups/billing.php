<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 03/08/2018
 * Time: 12:55
 */

$msisdn = '22573485669';
$numero = substr_replace($msisdn, "", 0, 3);
$ucip_url = 'http://10.184.38.10:10010/Air';
$transacID = mktime();

$montant = 1;


$value = (-($montant));
$methodName = 'UpdateBalanceAndDate';
$requete = '<?xml version="1.0"?>
        <methodCall><methodName>' . $methodName . '</methodName><params><param><value><struct>
        <member><name>originNodeType</name><value><string>EXT</string></value></member><member><name>originHostName</name><value><string>gbichsva</string></value></member>
        <member><name>originTransactionID</name><value><string>' . $transacID . '</string></value></member>
        <member><name>originTimeStamp</name><value><dateTime.iso8601>' . date('Ymd') . 'T' . date('h:m:i') . '+0000</dateTime.iso8601></value></member>
        <member><name>subscriberNumber</name><value><string>' . $numero . '</string></value></member><member><name>transactionCurrency</name><value><string>XOF</string></value></member>
        <member><name>adjustmentAmountRelative</name><value><string>' . $value . '</string></value></member><member><name>transactionCode</name><value><string>11</string></value></member>
        <member><name>transactionType</name><value><string>gbichcosms</string></value></member></struct></value></param></params></methodCall>';


$url = $ucip_url;

$header = array();
$header[] = 'Content-type: text/xml';
$header[] = 'Content-length: ' . strlen($requete);
$header[] = 'User-Agent: UGw Server/3.1/1.0';
$header[] = 'Authorization: Basic ' . base64_encode('gomedia:gomedia@2018');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ucip_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);
$data = curl_exec($ch);

echo $data;