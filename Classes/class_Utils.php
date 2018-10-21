<?php

class Utils extends Conn
{


    const UTILS_SIGN = 'GBICH SMS ************************';

    const UTILS_LINECNT = '1234567890123456789012345678901';

    const UTILS_ERROR_DB = 'CONNEXION A LA BASE IMPOSSIBLE!';

    const UTILS_ERROR_UN = 'SERVICE INACESSIBLE !';

    const UTILS_ERROR_AUTH = 'VOUS N\'ETES PAS AUTHORISE(E) A UTILISER CE SERVICE !';

    const UTILS_ERROR_SERVER = 'CE SERVICE A RENCONTRE UN PROBLEME. VEUILLEZ REESSAYER PLUS TARD !';

    public function __construct()
    {
    }

    public function logAction($log_msisdn_v, $log_type_v, $log_desc_v, $log_status_v, $log_option1_v = "null", $log_option2_v = "null", $log_option3_v = "null")
    {
        $return = FALSE;

        $log_action_date_d = date('Y-m-d H:i:s', time());

        $sql = "
                  INSERT INTO `melody_sva_db`.`logs_tb` (`log_msisdn_v`, `log_type_v`, `log_desc_v`, `log_status_v`, `log_action_date_d`,`log_option1_v`,`log_option2_v`,`log_option3_v`) 
                  VALUES ('$log_msisdn_v', '$log_type_v', '$log_desc_v', '$log_status_v', '$log_action_date_d', '$log_option1_v', '$log_option2_v', '$log_option3_v')
                ";

        $svaConn = $this->conn();

        $sql = $this->sqlPrepareStatement($sql);

        if(mysql_query($sql))
        {
            $return = TRUE;
        }

        @mysql_close($svaConn);

        return $return;
    }

    public function updateBalanceAndDateProcessMOOVUCIP($msisdn, $amount, $ucip_url, $TransId)
    {
        $numero = substr_replace($msisdn, "", 0, 3);
        $value = $amount;

        //$TransId = (mktime()+$msisdn);

        $methodName = 'UpdateBalanceAndDate';
        $requete = '<?xml version="1.0"?>
        <methodCall><methodName>' . $methodName . '</methodName><params><param><value><struct>
        <member><name>originNodeType</name><value><string>EXT</string></value></member><member><name>originHostName</name><value><string>gbichsva</string></value></member>
        <member><name>originTransactionID</name><value><string>' . $TransId . '</string></value></member>
        <member><name>originTimeStamp</name><value><dateTime.iso8601>' . date('Ymd') . 'T' . date('h:m:i') . '+0000</dateTime.iso8601></value></member>
        <member><name>subscriberNumber</name><value><string>' . $numero . '</string></value></member><member><name>transactionCurrency</name><value><string>XOF</string></value></member>
        <member><name>adjustmentAmountRelative</name><value><string>' . $value . '</string></value></member><member><name>transactionCode</name><value><string>11</string></value></member>
        <member><name>transactionType</name><value><string>gbichcosms</string></value></member></struct></value></param></params></methodCall>';

        //$url = 'http://10.184.38.10:10010/Air';
        $url = $ucip_url;
        $header[] = 'Content-type: text/xml';
        $header[] = 'Content-length: ' . strlen($requete);
        $header[] = 'User-Agent: UGw Server/3.1/1.0';

        $header[] = 'Authorization: Basic ' . base64_encode('gomedia:gomedia@2018');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);

        $data = curl_exec($ch);
        $retour = '';
        if(!curl_errno($ch))
        {   //retour OK
            $data = str_replace(' ', '', $data);
            $data = str_replace("\n", '', $data);
            //echo $data;
            //print_r($data);
            $pattern = '<member><name>responseCode</name><value><i4>0</i4></value></member>';
            if(strpos($data, $pattern) === false)
            {
                $retour = FALSE;
            } //erreur
            else
            {
                $retour = TRUE;
            } //success
        }
        else
        {
            $retour = NULL;
        }

        curl_close($ch); //fermeture de la connexion curl

        return $retour;
    }

    public function updateUsageThresholdsAndCountersProcessMOOVUCIP($msisdn, $amount, $TransId, $ucip_url)
    {
        $numero = substr_replace($msisdn, "", 0, 3);
        $value = $amount;

        //$TransId = mktime();

        $methodName = 'UpdateUsageThresholdsAndCounters';

        /*
         * exemple de la requete UpdateBalanceAndDate ...
        $requete = '<?xml version="1.0"?>
        <methodCall><methodName>' . $methodName . '</methodName><params><param><value><struct>
        <member><name>originNodeType</name><value><string>EXT</string></value></member><member><name>originHostName</name><value><string>gbichsva</string></value></member>
        <member><name>originTransactionID</name><value><string>' . $TransId . '</string></value></member>
        <member><name>originTimeStamp</name><value><dateTime.iso8601>' . date('Ymd') . 'T' . date('h:m:i') . '+0000</dateTime.iso8601></value></member>
        <member><name>subscriberNumber</name><value><string>' . $numero . '</string></value></member><member><name>transactionCurrency</name><value><string>XOF</string></value></member>
        <member><name>adjustmentAmountRelative</name><value><string>' . $value . '</string></value></member><member><name>transactionCode</name><value><string>11</string></value></member>
        <member><name>transactionType</name><value><string>gbichcosms</string></value></member></struct></value></param></params></methodCall>';
        */

        $requete = '<?xml version="1.0"?><methodCall><methodName>' . $methodName . '</methodName><params><param><value><struct>
        <member><name>originNodeType</name><value><string>EXT</string></value></member><member><name>originHostName</name><value><string>gbichsva</string></value></member>
        <member><name>originTransactionID</name><value><string>' . $TransId . '</string></value></member><member><name>originTimeStamp</name><value><dateTime.iso8601>' . date('Ymd') . 'T' . date('h:m:i') . '+0000</dateTime.iso8601></value></member>
        <member><name>subscriberNumber</name><value><string>' . $numero . '</string></value></member><member><name>transactionCurrency</name><value><string>XOF</string></value></member>
        <member><name>usageCounterUpdateInformation</name><value><array><data><value><struct><member><name>usageCounterID</name><value><int>7400</int></value></member>
        <member><name>adjustmentUsageCounterMonetaryValueRelative</name><value><string>' . $value . '</string></value></member></struct></value></data></array></value></member></struct></value></param></params></methodCall>';

        //$url = 'http://10.184.38.10:10010/Air';
        $url = $ucip_url;
        $header[] = 'Content-type: text/xml';
        $header[] = 'Content-length: ' . strlen($requete);
        $header[] = 'User-Agent: UGw Server/3.1/1.0';

        $header[] = 'Authorization: Basic ' . base64_encode('gomedia:gomedia@2018');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);

        $data = curl_exec($ch);
        $retour = '';
        if(!curl_errno($ch))
        {   //retour OK
            $data = str_replace(' ', '', $data);
            $data = str_replace("\n", '', $data);
            //echo $data;
            //print_r($data);

            //error_log("DATAAAAAAAAAA :".$data);

            $pattern = '<member><name>responseCode</name><value><i4>0</i4></value></member>';
            if(strpos($data, $pattern) === false)
            {
                $retour = FALSE;
            } //erreur
            else
            {
                $retour = TRUE;
            } //success
        }
        else
        {
            $retour = NULL;
        }

        curl_close($ch); //fermeture de la connexion curl

        return $retour;
    }

    public function loadExpiredMSISDNToRenew()
    {
        error_log("FONCTION loadExpiredMSISDNToRenew : RECUPERATION DE LA LISTE DES ABONNES A RECONDUIRE ... ");

        $_actual_day_timestamp = time(); //Heure système ...
        $_actual_day_date = date('Y-m-d', $_actual_day_timestamp);

        $allSubscriptions = NULL;

        $sql = "
                  SELECT * FROM melody_sva_db.subscriptions_tb 
                  WHERE datediff(DATE_FORMAT(CURDATE(), '%Y-%m-%d'), DATE_FORMAT(subscr_end_date_d, '%Y-%m-%d') ) >  '0'
                  AND subscr_status_n = 'ON' 
                  ORDER BY subscr_formule_type_n DESC,  subscr_end_date_d DESC
                ";

        //error_log("SQL loadExpiredMSISDNToRenew : ".$sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if($countNumrows = mysql_num_rows($res))
        {

            require_once('Classes/class_Subscriptions.php');


            while ($ligne = mysql_fetch_array($res))
            {

                $subscription = new Subscriptions();

                $subscription->setId_subscr_n($ligne['id_subscr_n']);
                $subscription->setSubscr_msisdn_v($ligne['subscr_msisdn_v']);
                $subscription->setSubscr_formule_type_n($ligne['subscr_formule_type_n']);
                $subscription->setSubscr_start_date_d($ligne['subscr_start_date_d']);
                $subscription->setSubscr_end_date_d($ligne['subscr_end_date_d']);
                $subscription->setSubscr_status_n($ligne['subscr_status_n']);

                //error_log("SOUSCRIPTEUR (loadExpiredMSISDNToRenew) ". $subscription->getSubscr_msisdn_v()." ".$subscription->getSubscr_start_date_d(). " ".$subscription->getSubscr_end_date_d());


                $allSubscriptions[] = $subscription;

            }


            error_log("FONCTION loadExpiredMSISDNToRenew : TOTAL ABONNES A RECONDUIRE : " . $countNumrows);

            @mysql_free_result($res);
        }
        @mysql_close($svaConn);

        return $allSubscriptions;

    }

    public function loadMSISDNToAlert()
    {

        $allSubscriptions = NULL;

        $sql = "SELECT * FROM melody_sva_db.subscriptions_tb WHERE datediff(DATE_FORMAT(CURDATE(), '%Y-%m-%d'), DATE_FORMAT(subscr_end_date_d, '%Y-%m-%d') ) = '0' ORDER BY subscr_formule_type_n DESC";

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if($countNumrows = mysql_num_rows($res))
        {

            require_once('Classes/class_Subscriptions.php');

            while ($ligne = mysql_fetch_array($res))
            {

                $subscription = new Subscriptions();

                $subscription->setId_subscr_n($ligne['id_subscr_n']);
                $subscription->setSubscr_msisdn_v($ligne['subscr_msisdn_v']);
                $subscription->setSubscr_formule_type_n($ligne['subscr_formule_type_n']);
                $subscription->setSubscr_start_date_d($ligne['subscr_start_date_d']);
                $subscription->setSubscr_end_date_d($ligne['subscr_end_date_d']);
                $subscription->setSubscr_status_n($ligne['subscr_status_n']);

                $allSubscriptions[] = $subscription;

            }

            @mysql_free_result($res);
        }
        @mysql_close($svaConn);

        return $allSubscriptions;

    }

    public function isIntoActiveSubscription($_subscr_msisdn_v)
    {
        //error_log("FUNCTION isIntoActiveSubscription: CHECKING IF MSISDN $_subscr_msisdn_v IS INTO AN ACTIVE SUBSCRIPTION ...");

        $return = FALSE;

        $_actual_day_timestamp = time(); //Heure système ...
        $_actual_day_date = date('Y-m-d H:i:s', $_actual_day_timestamp);

        $sql = "
                  SELECT * FROM melody_sva_db.subscriptions_tb 
                  WHERE subscr_start_date_d <= DATE_FORMAT('$_actual_day_date', '%Y-%m-%d %H:%i:%s') 
                  AND subscr_end_date_d >=  DATE_FORMAT('$_actual_day_date', '%Y-%m-%d %H:%i:%s')
                  AND subscr_msisdn_v = '$_subscr_msisdn_v' AND subscr_status_n = 'ON'
                ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if(mysql_num_rows($res))
        {

            $return = TRUE;

            @mysql_free_result($res);

            error_log("FUNCTION isIntoActiveSubscription: MSISDN $_subscr_msisdn_v IS INTO AN ACTIVE SUBSCRIPTION ... NOTHING TO DO !");
        }
        else
        {
            error_log("FUNCTION isIntoActiveSubscription: MSISDN $_subscr_msisdn_v IS NOT INTO AN ACTIVE SUBSCRIPTION ... ");
        }


        @mysql_close($svaConn);

        return $return;
    }

    public function asOnceATimeSubscribed($_subscr_msisdn_v)
    {
        //error_log("FUNCTION asOnceATimeSubscribed: CHECKING IF MSISDN $_subscr_msisdn_v AS ONCE TIME SUBSCRIBED ...");

        require_once('class_Subscriptions.php');


        $return = FALSE;

        $sql = "SELECT * FROM melody_sva_db.subscriptions_tb WHERE subscr_msisdn_v = '$_subscr_msisdn_v' ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if(mysql_num_rows($res))
        {

            $ligne = mysql_fetch_array($res);

            $subscription = new Subscriptions();

            $subscription->setId_subscr_n($ligne['id_subscr_n']);
            $subscription->setSubscr_msisdn_v($ligne['subscr_msisdn_v']);
            $subscription->setSubscr_formule_type_n($ligne['subscr_formule_type_n']);
            $subscription->setSubscr_start_date_d($ligne['subscr_start_date_d']);
            $subscription->setSubscr_end_date_d($ligne['subscr_end_date_d']);
            $subscription->setSubscr_status_n($ligne['subscr_status_n']);

            $return = $subscription;

            @mysql_free_result($res);

            error_log("FUNCTION asOnceATimeSubscribed: MSISDN $_subscr_msisdn_v AS ONCE TIME SUBSCRIBED ...!");

        }
        else
        {

            error_log("FUNCTION asOnceATimeSubscribed: MSISDN $_subscr_msisdn_v AS NEVER SUBSCRIBED ... FIRST TIME !");
        }

        @mysql_close($svaConn);

        return $return;
    }

    public function proceedSubscriptionbyUSSD($_subscr_msisdn_v, $_subscr_formule_type_n, $_subscr_status_n)
    {
        require_once('class_Formules.php');
        require_once('class_Subscriber.php');

        $formule = new Formules();
        $formule->loadFormule($_subscr_formule_type_n);

        $msg = "";

        $_subscr_srt_date_d_timestamp = time(); // Heure système ...
        $_subscr_end_date_d_timestamp = time() + (($formule->getFormuleNbdays() + $formule->getFormuleBonusdays()) * 24 * 60 * 60);
        $_subscr_notif_date_d_timestamp = time() + ((($formule->getFormuleNbdays() + $formule->getFormuleBonusdays()) - $formule->getFormuleNotifbefore()) * 24 * 60 * 60);

        $date_deb_insert = date('Y-m-d H:i:s', $_subscr_srt_date_d_timestamp);
        $date_fin_insert = date('Y-m-d H:i:s', $_subscr_end_date_d_timestamp);

        error_log("date_deb_insert : " . $date_deb_insert);
        error_log("date_fin_insert : " . $date_fin_insert);

        $sql = "INSERT INTO `melody_sva_db`.`subscriptions_tb` (`subscr_msisdn_v`, `subscr_formule_type_n`, `subscr_start_date_d`, `subscr_end_date_d`, `subscr_status_n`) VALUES ('$_subscr_msisdn_v', '$_subscr_formule_type_n', '$date_deb_insert', '$date_fin_insert', '$_subscr_status_n')";

        $svaConn = $this->conn();

        if(mysql_query($sql))
        {
            $date_debut = date('d-m-Y');
            $date_fin = date('d-m-Y', $_subscr_end_date_d_timestamp);

            $date_notif = date('Y-m-d H:i:s', $_subscr_notif_date_d_timestamp);


            error_log("ENREGISTREMENT DU SOUSCRIPTEUR " . $_subscr_msisdn_v);
            $subscriber = new Subscriber();
            $subscriber->setSubscriber($_subscr_msisdn_v, $date_notif, "ON");

            //error_log("SUBSCRIBER WILL BE NOTIFIED ON : ".$date_notif);


            $msg = 'ZEPAPARAAA !!! Votre opération ' . $formule->getFormuleLabel() . ' a été effectuée avec succès. Valable du ' . $date_debut . ' au ' . $date_fin . '.';
            $msg .= " Vous bénéficiez d'une semaine gratuite !";

            //Envoi de SMS ...
            //$tools = new Utils();
            //$tools->sendSMSMOOVKannel("GBICH SMS", $_subscr_msisdn_v,$msg);

            //Admin purposes monitoring ...
            //$allSubscr = $tools->loadValidSubscribersMSISDN();
            //$admSMS = count($allSubscr)." SUBSCRIBERS ADDED - CURRENT MSISDN : ".$_subscr_msisdn_v;
            //$tools->sendSMSMOOVKannel("GBICH ADDs", '22552390202',$admSMS);

        }
        else
        {
            //Envoi de SMS ...
            $admSMS = "FAILED TO ADD NEW SUBSCRIBER - MSISDN : " . $_subscr_msisdn_v . " !!!";
            $this->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $admSMS);
        }

        @mysql_close($svaConn);

        return $msg;
    }

    public function updateSubscriptionbyUSSD($subscr)
    {

        $return = FALSE;

        require_once('class_Formules.php');

        $subscrToUpdate = $subscr;

        $formule = new Formules();
        $formule->loadFormule($subscrToUpdate->getSubscr_formule_type_n());

        $_subscr_srt_date_d_timestamp = time(); // Heure système ...
        $_subscr_end_date_d_timestamp = time() + ($formule->getFormuleNbdays() * 24 * 60 * 60);

        $date_deb_insert = date('Y-m-d H:i:s', $_subscr_srt_date_d_timestamp);
        $date_fin_insert = date('Y-m-d H:i:s', $_subscr_end_date_d_timestamp);

        $sql = "
                    UPDATE `melody_sva_db`.`subscriptions_tb` 
                    SET `subscr_start_date_d`='$date_deb_insert', `subscr_end_date_d`='$date_fin_insert', `subscr_status_n`='ON' 
                    WHERE `id_subscr_n`='" . $subscrToUpdate->getId_subscr_n() . "' 
                    and`subscr_msisdn_v`='" . $subscrToUpdate->getSubscr_msisdn_v() . "' 
                    and`subscr_start_date_d`='" . $subscrToUpdate->getSubscr_start_date_d() . "' 
                    and`subscr_end_date_d`='" . $subscrToUpdate->getSubscr_end_date_d() . "'   
                ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        if(mysql_query($sql))
        {

            $return = TRUE;
            error_log("SUBSCRIPTION UPDATE SUCCEDED FOR " . $subscrToUpdate->getSubscr_msisdn_v() . " - RENEWED FROM $date_deb_insert TO $date_fin_insert !");

        }
        else
        {
            error_log("SUBSCRIPTION UPDATED FAILED FOR " . $subscrToUpdate->getSubscr_msisdn_v() . " ... SQL: " . $sql);
        }

        @mysql_close($svaConn);

        return $return;
    }

    public function unsuscribe($id, $msisdn, $startDate, $endDate)
    {

        $return = FALSE;

        $sql = "
                    UPDATE `melody_sva_db`.`subscriptions_tb` 
                    SET `subscr_status_n`='OFF' 
                    WHERE `id_subscr_n`= '$id' 
                    and`subscr_msisdn_v`= '$msisdn'
                    and`subscr_start_date_d`= '$startDate' 
                    and`subscr_end_date_d`= '$endDate'  
                ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        if(mysql_query($sql))
        {

            $return = TRUE;
            error_log("SUBSCRIPTION DEACTIVATION ----------------- SUCCEDED FOR $msisdn !");

        }
        else
        {
            error_log("SUBSCRIPTION DEACTIVATION ----------------- FAILED FOR $msisdn ... SQL: " . $sql);
        }

        @mysql_close($svaConn);

        return $return;
    }

    private function sqlPrepareStatement($sql)
    {
        $ql = trim($sql);
        $ql = str_replace(' ', '', $sql);

        return $sql;
    }

    public function loadGBICHSMS($rubr)
    {
        //error_log("RECUPERATION DU SMS POUR DIFFUSION : " . $rubr);

        $return = FALSE;

        $_actual_day_timestamp = time(); //Heure système ...
        $_actual_day_date = date('Y-m-d H:i', $_actual_day_timestamp);

        $sql = "
                    SELECT rubctnt.rubr_ctnt_id_n, rubr.rubric_name_v, rubctnt.rubr_ctnt_text_v, rubr.rubric_daily_count_n, rubctnt.rubr_ctnt_date_d  
                    FROM melody_sva_db.rubrics_contents_tb rubctnt,  melody_sva_db.rubrics_tb rubr
                    WHERE rubr.rubric_keyword_v = rubctnt.rubric_keyword_v
                    AND rubctnt.rubric_keyword_v = '$rubr'
                    AND DATE_FORMAT(rubctnt.rubr_ctnt_date_d,'%Y-%m-%d %H:%i') = DATE_FORMAT('$_actual_day_date','%Y-%m-%d %H:%i')
                    AND rubctnt.rubr_ctnt_flag_v = 'ON' 
                    ORDER BY rubctnt.rubr_ctnt_id_n DESC
                ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if(mysql_num_rows($res))
        {
            error_log("SMS RECUPERE, POUR CETTE RUBRIQUE " . $rubr . " ... ENVOI EN COURS ...");

            require_once('class_SMS.php');
            $sms = new SMS();

            if($ligne = mysql_fetch_array($res))
            {
                $sms->setRubrId($ligne['rubr_ctnt_id_n']);
                $sms->setRubrName($ligne['rubric_name_v']);
                $sms->setRubrCtntText($ligne['rubr_ctnt_text_v']);

                // Mise à jour du statut du SMS ...
                //UPDATE `melody_sva_db`.`rubrics_contents_tb` SET `rubr_ctnt_flag_v`='OFF' WHERE `rubr_ctnt_id_n`='123';

                $sqlUpdate = "UPDATE `melody_sva_db`.`rubrics_contents_tb` SET `rubr_ctnt_flag_v`='OFF' WHERE `rubr_ctnt_id_n`='" . $ligne['rubr_ctnt_id_n'] . "'";

                $resUpdate = mysql_query($sqlUpdate) or exit(mysql_error());

                if(mysql_affected_rows())
                {
                    $return = $sms;
                }

                @mysql_free_result($resUpdate);

                // ENUM("BLAGUE","FOULOSOPHIE","BOOST","INFO","AFERAJ'")
            }


            @mysql_free_result($res);

        }
        else
        {

            //error_log("AUCUN SMS RECUPERE, POUR CETTE RUBRIQUE " . $rubr . " ... ABANDON !");
        }

        @mysql_close($svaConn);

        return $return;

    }

    public function loadBillingCountLeftOfThisDay()
    {
        $return = 0;

        $sql = "SELECT 500000 - (SELECT count(*) FROM melody_sva_db.logs_tb WHERE log_type_v = 'BILLING' AND log_status_v ='SUCCESS' AND DATE_FORMAT(log_action_date_d, '%Y-%m-%d') = DATE_FORMAT(CURDATE(), '%Y-%m-%d') )  as billingLeftThisDay";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if(mysql_num_rows($res))
        {

            if($ligne = mysql_fetch_array($res))
            {

                $return = $ligne['billingLeftThisDay'];
            }

            @mysql_free_result($res);
        }

        @mysql_close($svaConn);

        error_log("FONCTION loadBillingCountLeftOfThisDay :  " . $return);

        return $return;
    }

    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function loadValidSubscribersMSISDN()
    {

        $_actual_day_timestamp = time(); //Heure système ...
        $_actual_day_date = date('Y-m-d H:i:s', $_actual_day_timestamp);

        $_actual_day_date_human = date('d-m-Y', $_actual_day_timestamp);

        error_log("FONCTION loadValidSubscribersMSISDN : RECUPERATION DE LA LISTE DES ABONNES DONT LA SOUSCRIPTION EST ACTIVE CE ... " . $_actual_day_date_human);

        $subscribers = array();

        $sql = "
                  SELECT DISTINCT subscr_msisdn_v 
                  FROM melody_sva_db.subscriptions_tb 
                  WHERE subscr_status_n = 'ON'
                  /*AND subscr_start_date_d <= DATE_FORMAT('$_actual_day_date', '%Y-%m-%d') 
                  AND subscr_end_date_d >=  DATE_FORMAT('$_actual_day_date', '%Y-%m-%d')*/
                  ORDER BY id_subscr_n DESC
               ";

        $sql = $this->sqlPrepareStatement($sql);

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if(mysql_num_rows($res))
        {
            $nbrSouscr = 0;

            while ($ligne = mysql_fetch_array($res))
            {
                $subscribers[] = $ligne['subscr_msisdn_v'];

                $nbrSouscr++;
            }

            error_log("FONCTION loadValidSubscribersMSISDN :  NBRE TOTAL : " . $nbrSouscr);

            @mysql_free_result($res);

            //var_dump($subscribers);

        }

        @mysql_close($svaConn);

        return $subscribers;

    }

    public function check_in_range($start_date, $end_date, $date_from_user)
    {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }

    public function cleanString($string)
    {
        $string = str_replace('é', 'e', $string);
        $string = str_replace('è', 'e', $string);
        $string = str_replace('ê', 'e', $string);

        $string = str_replace('à', 'a', $string);
        $string = str_replace('â', 'a', $string);

        $string = str_replace('Ç', 'C', $string);
        $string = str_replace('ç', 'c', $string);

        $string = str_replace('î', 'i', $string);


        $string = str_replace('Ï', 'I', $string);
        $string = str_replace('ï', 'i', $string);

        $string = str_replace('ô', 'o', $string);

        $string = str_replace('û', 'u', $string);

        $string = str_replace('œ', 'oe', $string);


        $string = preg_replace('/\s\s+/', ' ', $string);
        $string = str_replace("’", "'", $string);

        //preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return $string;
    }

    public function teststrings($my_string)
    {
        $tab = array("UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1", "ISO-8859-6", "CP1256");
        $chain = "";
        foreach ($tab as $i)
        {
            foreach ($tab as $j)
            {
                $chain .= " $i$j " . iconv($i, $j, "$my_string");
            }
        }

        return $chain;
    }

    public function sendSMSMOOVKannel($emetteur, $destinataire, $message)
    {
        $msisdn = str_replace("225", "", $destinataire);

        $url = 'http://192.168.53.2:14013/cgi-bin/sendsms?username=svam&password=svam&text=' . urlencode($this->cleanString($message)) . '&from=' . urlencode($emetteur) . '&to=' . $msisdn . '&smsc=smsc8&coding=0';

        file_get_contents($url);
    }

    public function sendSMSMOOVKannelTESTS($emetteur, $destinataire, $message, $encoding = '0')
    {
        $msisdn = str_replace("225", "", $destinataire);

        $url = 'http://192.168.53.2:14013/cgi-bin/sendsms?username=svam&password=svam&text=' . urlencode($message) . '&from=' . urlencode($emetteur) . '&to=' . $msisdn . '&smsc=smsc8&coding=' . $encoding;

        file_get_contents($url);
    }

    public function executeAPI($lien)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $lien);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        $api_result = curl_exec($curl);

        if(curl_errno($curl))
        {
            return 'API Execution error : ' . curl_error($curl);
        }

        parse_str($api_result, $out);

        $str = "";

        foreach ($out as $param => $value)
        {
            $str .= $param . $value;
        }

        $return_value = $str;

        //$return_value = str_replace('_', ' ', $str);
        //$this->writeLog("EXECUTE API RETURN VALUE :".$return_value);

        return $return_value;

        curl_close($curl);
    }

    public function showContent($content, $svc_desc)
    {
        $tab = explode("$", $content);

        foreach ($tab as $contents => $value)
        {
            echo ($value) . PHP_EOL;
        }
    }

    public function writeLog($message, $file = 'melody.log')
    {
        //$file = $file."-".date('dmY-His', time());

        if(!($pointeur = fopen('Logs/' . $file, 'a')))
        {
            error_log("FUNCTION writeLog: can't create file log $file !");
        }
        else
        {
            if(!($octets = fwrite($pointeur, date('d-m-Y H:i:s', time()) . " " . $message . "\n")))
            {
                error_log("FUNCTION writeLog: can't write into $file !");
            }
            else
            {
                return "SUCCES: " . $octets . " octet(s) !";
            }
        }
        fclose($pointeur);
    }

    public function getLastAction($svc, $ph, $tid)
    {
        $id = $this->conn();

        $requete = "
						SELECT * FROM melody_logs_tb
						WHERE msisdn_v = '$ph'
						AND session_tid = '$tid'
						AND service_code_called_v = '$svc'
						ORDER BY action_date_log_v DESC
					";

        $resultat = mysql_query($requete) or die(mysql_error());

        mysql_close($id);
    }

    public function getLastLogActionDate($ph)
    {
        $id = $this->conn();

        $requete = "
					SELECT *
					FROM melody_logs_tb
					WHERE msisdn_v = '$ph' AND user_input_v = 'changeTarifPlanDone'
					ORDER BY id_n DESC
				";

        if($resultat = mysql_query($requete))
        {
            if(mysql_num_rows($resultat) !== 0)
            {
                if($ligne = mysql_fetch_array($resultat))
                {
                    return $ligne['action_date_log_v'];
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            die(mysql_error());
        }

        mysql_close($id);
    }

    public function datefr2en($mydate)
    {
        @list ($jour, $mois, $annee) = explode('/', $mydate);
        return @date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
    }

    public function timeChecker($date_french_format, $timestamp_in_minutes)
    {
        if($date_french_format)
        {
            $parts = explode(" ", $date_french_format);

            $dateAnglais = $this->datefr2en($parts[0]) . " " . $parts[1];

            $timestamp_dateAnglais = strtotime($dateAnglais);

            $timestamp_dateActuelle = time();

            $timestamp_dateEcoulee = $timestamp_dateActuelle - $timestamp_dateAnglais;

            $tempsEcouleEnMinutes = $timestamp_dateEcoulee / 60;

            if($tempsEcouleEnMinutes < $timestamp_in_minutes)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    public function billingSuccessActions($msisdn, $log_type, $log_desc, $log_status, $amount, $transID)
    {
        require_once('class_Subscriptions.php');

        $thisSubscr = new Subscriptions();
        $thisSubscr->loadSubscription($msisdn);

        $_date_deb = $thisSubscr->getSubscr_start_date_d();
        $_date_fin = $thisSubscr->getSubscr_end_date_d();

        // Logging ...
        $this->logAction($msisdn, $log_type, $log_desc, $log_status, (-($amount)), $amount, $transID);


        // Send SMS ...
        $SMS = "ZEPAPARAAA !! Souscription $log_desc reconduite avec succes, valable du $_date_deb au $_date_fin !";
        $this->sendSMSMOOVKannel("GBICH SMS", $msisdn, $SMS);

        $msg = "FUNCTION billingSuccessActions: +++++++++++++ BILLING SUBSCRIBER :  SUCCEDED ... ACTION ($log_type $log_desc)  FOR THIS MSISDN ----> " . $msisdn;
        $this->writeLog($msg, "renewSubscriptions.log");

        error_log($msg);

    }

    public function billingFailureActions($msisdn, $log_type, $log_desc, $log_status, $amount, $transID)
    {

        // Logging ...
        $this->logAction($msisdn, $log_type, $log_desc, $log_status, (-($amount)), $amount, $transID);

        // Send SMS ...
        //$SMS = "ZEPAPARAAA !! Credit insuffisant pour le reabonnement automatique a ta souscription $log_desc . Recharge viiite dehh !";
        //$tools->sendSMSMOOVKannel("GBICH SMS", $msisdn, $SMS);

        //error_log("######### ******* BILLING SUBSCRIBER :  FAILED ... ACTION ($log_type $log_desc)  FOR THIS MSISDN ----> " . $msisdn);

    }

}
