<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 12/07/2018
 * Time: 00:42
 */


$nowTimestamp   = time();
$rslt = new DateTime();
$rslt = $rslt->format(DateTime::ISO8601);
$madate=preg_replace('#-#', '', $rslt);

$requestArrays = array
(
    'originNodeType'=>'EXT',
    'originHostName'=>'gbichsva',
    'originTransactionID'=>$nowTimestamp,
    'originTimeStamp'=>$madate,
    'subscriberNumber'=>'52390202',
    'transactionCurrency'=>'XOF',
    'adjustmentAmountRelative'=>'-100',
    'transactionCode'=>'11',
    'transactionType'=>'gbichsms'
);



# Using the XML-RPC extension to format the XML package
$request = xmlrpc_encode_request("UpdateBalanceAndDate", $requestArrays);

# Using the cURL extension to send it off,
# first creating a custom header block
$header[0] = "Content-type: text/xml";
$header[1] = "Content-length: ".strlen($request) . "\r\n";
$header[2] = "User-Agent: Mozilla/4.1/1.0";
$header[3] = "Authorization: Basic".base64_encode("gomedia:gomedia@2018");


$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, "http://10.184.38.10:10011/Air"); # URL to post to
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
curl_setopt( $ch, CURLOPT_HTTPHEADER, $header ); # custom headers, see above
curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/4.1/1.0');
curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
$result = curl_exec( $ch ); # run!
curl_close($ch);
echo $result;