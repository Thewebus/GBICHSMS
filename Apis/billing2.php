<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 11/07/2018
 * Time: 20:27
 */

$useragentautomatic = $_SERVER['HTTP_USER_AGENT'];

//error_log("USERAGTN: ". $useragentautomatic);
//$useragentautomatic = "Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0";

$nowTimestamp   = time();
//$nowDate        = date('Y-m-d H:i:s', $_subscr_srt_date_d_timestamp);

$rslt = new DateTime();
$rslt = $rslt->format(DateTime::ISO8601);
$madate=preg_replace('#-#', '', $rslt);

xmlrpc_set_type($madate, "datetime");

//error_log("madate: ". $madate);


$xmlrpc_reqs = array
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



$request = xmlrpc_encode_request("UpdateBalanceAndDate", $xmlrpc_reqs);

$context = stream_context_create(array('http' => array(
    'method' => 'POST',
    'header' => 'Content-Type: text/xml'
        . 'Content-Length: '.strlen($request)."\r\n"
        . 'User-Agent: Mozilla/4.1/1.0'

    . "Authorization: ".base64_encode("gomedia:gomedia@2018"),
    'content' => $request
)));


$file = file_get_contents("http://10.184.38.10:10011/Air", true, $context);

$response = xmlrpc_decode($file);

if ($response && xmlrpc_is_fault($response)) {
    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
} else {
    print_r($response);
}