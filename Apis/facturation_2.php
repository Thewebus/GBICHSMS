<?php


function facturation_f($numero, $value)
{
    $TransId = mktime();
    $requete = '<?xml version="1.0"?>
<methodCall><methodName>UpdateBalanceAndDate</methodName><params><param><value><struct>
<member><name>originNodeType</name><value><string>EXT</string></value></member><member><name>originHostName</name><value><string>bgichsva</string></value></member>
<member><name>originTransactionID</name><value><string>'.$TransId.'</string></value></member>
<member><name>originTimeStamp</name><value><dateTime.iso8601>'.date('Ymd').'T'.date('h:m:i').'+0000</dateTime.iso8601></value></member>
<member><name>subscriberNumber</name><value><string>'.$numero.'</string></value></member><member><name>transactionCurrency</name><value><string>XOF</string></value></member>
<member><name>adjustmentAmountRelative</name><value><string>'.$value.'</string></value></member><member><name>transactionCode</name><value><string>11</string></value></member>
<member><name>transactionType</name><value><string>gbichcosms</string></value></member></struct></value></param></params></methodCall>';

    $url = 'http://10.184.38.10:10010/Air';
    $header[] = 'Content-type: text/xml';
    $header[] = 'Content-length: '.strlen($requete);
    $header[] = 'User-Agent: UGw Server/3.1/1.0';

    $header[] = 'Authorization: Basic '.base64_encode('gomedia:gomedia@2018');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);

    $data = curl_exec($ch);
    $retour = '';
    if (!curl_errno($ch)) {   //retour OK
        $data = str_replace(' ', '', $data);
        $data = str_replace("\n", '', $data);
        //echo $data;
        //print_r($data);
        $pattern = '<member><name>responseCode</name><value><i4>0</i4></value></member>';
        if (strpos($data, $pattern) === false) {
            $retour = '1';
        } //erreur
        else {
            $retour = '0';
        } //success
    } else {
        $retour = '-1';
    }

    curl_close($ch); //fermeture de la connexion curl
    return $retour;
}

$numero = '52390202';

$montant = 10;

echo $r = facturation_f($numero, (-$montant));

