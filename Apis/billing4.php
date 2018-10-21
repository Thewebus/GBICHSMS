<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 12/07/2018
 * Time: 10:44
 */

$rslt=new DateTime();
$rslt= $rslt->format(DateTime::ISO8601);
$madate=preg_replace('#-#', '', $rslt);
xmlrpc_set_type($madate, "datetime");


$requete=xmlrpc_encode_request('UpdateBalanceAndDate',array(
    'originNodeType'=>'EXT',
    'originHostName'=>'gbichsva',
    'originTransactionID'=>'14598747854741',
    'originTimeStamp'=>$madate,
    'subscriberNumber'=>'22552390202',
    'transactionCurrency'=>'XOF',
    'adjustmentAmountRelative'=>'-10',
    'transactionCode'=>'11',
    'transactionType'=>'gbich'
));

$requete = '<?xml version="1.0"?>
<methodCall>
<methodName>UpdateBalanceAndDate</methodName>
<params>
 <param>
  <value>
   <struct>
    <member>
     <name>originNodeType</name>
     <value>
      <string>EXT</string>
     </value>
    </member>
    <member>
     <name>originHostName</name>
     <value>
      <string>gbichsva</string>
     </value>
    </member>
    <member>
     <name>originTransactionID</name>
     <value>
      <string>20182018</string>
     </value>
    </member>
    <member>
     <name>originTimeStamp</name>
     <value>
      <dateTime.iso8601>201807122T10:51:21+0200</dateTime.iso8601>
     </value>
    </member>
    <member>
     <name>subscriberNumber</name>
     <value>
      <string>22552390202</string>
     </value>
    </member>
    <member>
     <name>transactionCurrency</name>
     <value>
      <string>XOF</string>
     </value>
    </member>
    <member>
     <name>adjustmentAmountRelative</name>
     <value>
      <string>-10</string>
     </value>
    </member>
    <member>
     <name>transactionCode</name>
     <value>
      <string>11</string>
     </value>
     </member>
     <member>
      <name>transactionType</name>
      <value>
       <string>gbichcosms</string>
      </value>
     </member>
    </struct>
   </value>
  </param>
 </params>
</methodCall>';


$headers=array('Content-Type:text/xml','Content-Length:'.strlen($requete),'User-Agent:Mozilla/4.1/1.0','Authorization:'.base64_encode("gomedia:gomedia@2018"));

$c = curl_init();
curl_setopt($c, CURLINFO_HEADER_OUT, true);
curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
curl_setopt($c, CURLOPT_VERBOSE, true);
curl_setopt($c, CURLOPT_URL, '10.184.38.10:10011/Air');/
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
/*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
/*On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour*/
curl_setopt($c, CURLOPT_HEADER, true);
/*On indique à curl d'envoyer une requete post*/
curl_setopt($c, CURLOPT_POST,true);
/*On donne les paramêtre de la requete post*/
curl_setopt($c,CURLOPT_POSTFIELDS, $requete);
/*On execute la requete*/

$output = curl_exec($c);
/*$information = curl_getinfo($c);
print_r($information);*/

//echo $output;