<?php

//include('xmlrpc.php');

/*
$test ='<?xml version="1.0" encoding="iso-8859-1"?><note>
        <to>Tove</to>
        <from>Jani</from>
        <heading>Reminder</heading>
        <body>Don\'t forget me this weekend!</body>
        </note>';
*/


//header('Content-Type: text/xml');

/*
$data = array(
    'originNodeType'=>'EXT',
	'originHostName'=>'gbichsva',
	'originTransactionID'=>'test_gbich_sva_8',
	'originTimeStamp'=>time(),
	'subscriberNumberNAI'=>0,
	'subscriberNumber'=>'52390202',
	'messageCapabilityFlag'=>array(
			'promotionNotificationFlag'=>0,
			'firstIVRCallSetFlag'=>0,
			'accountActivationFlag'=>0
		),
	'requestedInformationFlags'=>array(
		'requestMasterAccountBalanceFlag'=>0,
		'allowedServiceClassChangeDateFlag'=>0,
		'requestLocationInformationFlag'=>0
		),
	'requestPamInformationFlag'=>1,
	'requestActiveOffersFlag'=>0,
	'requestAttributesFlag'=>0,
	'negotiatedCapabilities'=>0

);

*/


$rslt = new DateTime();

$rslt = $rslt->format(DateTime::ISO8601);

$madate=preg_replace('#-#', '', $rslt);

xmlrpc_set_type($madate, "datetime");



$requete = xmlrpc_encode_request
(
    'UpdateBalanceAndDate',
    array
    (
        'originNodeType'=>'EXT',
        'originHostName'=>'gbichsva',
        'originTransactionID'=>'1',
        'originTimeStamp'=>$madate,
        'subscriberNumber'=>'52390202',
        'transactionCurrency'=>'XOF',
        'adjustmentAmountRelative'=>'-100',
        'transactionCode'=>'11',
        'transactionType'=>'gbichsms'
    )
);





$headers = array(
    "Content-Type: text/xml",
    "Content-Length: ".strlen($requete). "\r\n",
    //"User-Agent: Mozilla/4.1/1.0",
    "Authorization: BASIC ".base64_encode("gomedia:gomedia@2018")
);



/*
$useragentautomatic = $_SERVER['HTTP_USER_AGENT'];
$headers[] = "Content-Type: text/xml";
$headers[] = "Content-Length: ".strlen($requete). "\r\n";
$headers[] = "User-Agent: ".$useragentautomatic;
$headers[] = "Authorization: ".base64_encode("gomedia:gomedia@2018");
*/

//echo $requete;


$c = curl_init();
curl_setopt($c, CURLOPT_URL, '10.184.38.10:10011/Air');
curl_setopt($c, CURLINFO_HEADER_OUT, true);
curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
curl_setopt($c, CURLOPT_VERBOSE, true);
curl_setopt($c, CURLOPT_POST,true); /*On indique à curl d'envoyer une requete post*/
curl_setopt($c,CURLOPT_POSTFIELDS, $requete); /*On donne les paramêtre de la requete post*/



curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/4.1/1.0');
curl_setopt($c, CURLOPT_USERPWD, 'Basic '.base64_encode("gomedia:gomedia@2018"));


//curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true); /*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
//curl_setopt($c, CURLOPT_HEADER, true); /*On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour*/

$output = curl_exec($c); /*On execute la requete*/

//echo $output;

$information = curl_getinfo($c);
print_r($information);




