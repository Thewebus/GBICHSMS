<?php

/**
 *
 * @author Jacques GOUDE
 * @since 2.0
 */

class MelodyUSSD
{


    private $_sc;
    private $_lang;
    private $_msisdn;
    private $_req_no;
    private $_session_id;

    private $_user_input;

    private $_screen_id;
    private $_session_op;
    private $_screen_text;
    private $_options = array();

    private $_arrival = "";
    private $_content = "";


    public function __construct()
    {
        $this->initialize();
    }

    public function run()
    {
        /**
         */
        /*
         * Processing ...
         * /*****************************************************************
         */


        if($this->get_sc() != "")
        {// Access by USSD ...

            // Loading Database's datas ...
            require_once('class_USSDBDDatas.php');

            $bddatas = new USSDBDDatas();

            if($bddatas->getDBContents($this->get_screen_id(), $this->get_user_input()))
            {

                $this->set_screen_id($bddatas->get_screen_id());
                $this->set_screen_text($bddatas->get_screen_text());
                $this->set_options($bddatas->get_options());
                $this->set_session_op($bddatas->get_session_op());


                ///Gestion du retour ...

                if($this->get_session_op() == "continue")
                {
                    $USSDSender = new USSDSender();
                    $USSDSender->load($this->get_screen_id(), $this->get_session_op(), utf8_encode($this->get_screen_text()), $this->get_options());
                    $USSDSender->send();
                }
                else
                {

                    switch ($input = $this->get_user_input())
                    {

                        case '1': //Souscription hebdo ...
                        case '2': //Souscription mois ...

                            $this->subscriptionByUSSD($input);

                            break;


                        case '3': //Souscription annulée ...

                            $this->subscriptionAbort();
                            break;

                        default:

                            $this->errorChoice();
                            error_log("ERREUR CHOIX !");

                    }

                }
            }


        }
        else
        { // Access by SMS ...

            switch ($smsContent = strtolower(trim($this->getSMSContent())))
            {
                case 'semaine': //Souscription hebdo ...
                    //$this->subscriptionBySMS('1');
                    error_log("ERROR ... " . $smsContent);
                    $this->SMSSyntaxError();
                    break;

                case 'mois': //Souscription mois ...
                    //$this->subscriptionBySMS('2');
                    error_log("ERROR ... " . $smsContent);
                    $this->SMSSyntaxError();
                    break;

                case 'stop': // De Souscription ...
                    error_log("UNSUSCRIBE TRIGGER ....... FOR MSISDN  -> " . $this->get_msisdn());
                    $this->unSubscriptionBySMS();
                    break;

                default:
                    error_log("ERROR ... " . $smsContent);
                    $this->SMSSyntaxError();
            }
        }

    }


    /* InnerProcess *********************************************************** */

    private function choosing()
    {
        $USSDSender = new USSDSender();
        $USSDSender->load($this->get_screen_id(), $this->get_session_op(), utf8_encode($this->get_screen_text()), $this->get_options());
        $USSDSender->send();
    }

    private function subscriptionByUSSD($user_input)
    {

        $tools = new Utils();

        $formule = new Formules();
        $USSDSender = new USSDSender();

        $url1 = 'http://10.184.38.10:10010/Air';
        $url2 = 'http://10.184.38.11:10010/Air';

        //$subscrType = ($user_input == "1") ? ("SEMAINE") : ("MOIS");

        if($subscription = $tools->asOnceATimeSubscribed($this->get_msisdn()))
        {

            if($tools->isIntoActiveSubscription($this->get_msisdn()) == 'TRUE')
            {

                $USSDSender->sendCustom("GBICH SMS: Vous avez deja une souscription active !");

            }
            else
            {
                //Todo: Essayer de facturer et re abonner si le solde le permet ... Sinon Message: "vous n'avez pas assez de credit pour effectuer cette operation" ...

                $formule->loadFormule($user_input);
                $transID = (mktime() + $this->get_msisdn());

                if($tools->updateBalanceAndDateProcessMOOVUCIP($this->get_msisdn(), (-($formule->getFormulePrice())), $url1, $transID) && $tools->updateSubscriptionbyUSSD($subscription))
                {

                    $tools->billingSuccessActions($this->get_msisdn(), 'BILLING', $formule->getFormuleType(), 'SUCCESS', $formule->getFormulePrice(), $transID);

                    $USSDSender->sendCustom("Votre abonnement a ete reconduit avec succes !");
                    error_log("***** *****  >>>>>>>>>>>>  RE SOUSCRIPTION EFFECTUEE AVEC SUCCES ***** MSISDN ->" . $this->get_msisdn());
                }
                elseif($tools->updateBalanceAndDateProcessMOOVUCIP($this->get_msisdn(), (-($formule->getFormulePrice())), $url2, $transID) && $tools->updateSubscriptionbyUSSD($subscription))
                {

                    $tools->billingSuccessActions($this->get_msisdn(), 'BILLING', $formule->getFormuleType(), 'SUCCESS', $formule->getFormulePrice(), $transID);

                    $USSDSender->sendCustom("Votre abonnement a ete reconduit avec succes !");
                    error_log("***** *****  >>>>>>>>>>>>  SOUSCRIPTION RECONDUITE AVEC SUCCES ***** MSISDN ->" . $this->get_msisdn());
                }
                else
                {

                    $tools->billingFailureActions($this->get_msisdn(), 'BILLING', $formule->getFormuleType(), 'FAILED', $formule->getFormulePrice(), $transID);

                    $USSDSender->sendCustom("Vous n'avez pas assez de credit pour effectuer cette operation !");
                    error_log("***** *****  >>>>>>>>>>>>  SOUSCRIPTION ECHOUEE, SOLDE INSUFFISANT **** MSISDN ->" . $this->get_msisdn());
                }


            }

        }
        else
        {

            ///GRATUIT ...

            $message = $tools->proceedSubscriptionbyUSSD($this->get_msisdn(), $user_input, 'ON');

            $USSDSender->load($this->get_screen_id(), $this->get_session_op(), $message, $this->get_options());

            $USSDSender->send();

            error_log("***** ***** NEW SUBSCRIBER ***** HOURAAAAAAA !!!! SOUSCRIPTION VIA USSD EFFECTUEE POUR MSISDN ->" . $this->get_msisdn() . " ***** *****");

        }

    }

    private function subscriptionBySMS($user_input)
    {

        $tools = new Utils();
        $USSDSender = new USSDSender();

        $subscrType = ($user_input == "1") ? ("SEMAINE") : ("MOIS");

        if($tools->isIntoActiveSubscription($this->get_msisdn()) == FALSE)
        {

            ///GRATUIT ...
            if($this->billingProcess($this->get_msisdn()))
            {

                $message = $tools->proceedSubscriptionbyUSSD($this->get_msisdn(), $user_input, 'ON');

                $USSDSender->load($this->get_screen_id(), $this->get_session_op(), $message, $this->get_options());

                error_log("*****  >>>>>>>>>>>>  SOUSCRIPTION VIA USSD " . $subscrType . " EFFECTUEE POUR MSISDN ->" . $this->get_msisdn());
            }

            ///FACTURATION ...
            /*
            if($this->billingProcessMOOVUCIP($this->get_msisdn(),(-(150)),"http://10.184.38.10:10010/Air"))
            {
                $souscription = new Subscriptions();
                $message = $souscription->proceed($this->get_msisdn(), $user_input,'ON');

                $USSDSender->load($this->get_screen_id(), $this->get_session_op(), $message, $this->get_options());

                error_log("BILLING DONE ON ". $this->get_msisdn()." ... WEEK.");
            }
            else {

                $USSDSender->sendCustom("GBICH SMS: CREDIT INSUFFISANT ! VEUILLEZ RECHARGER SVP !");
            }

            */

            $USSDSender->send();

        }
        else
        {

            $USSDSender->sendCustom("GBICH SMS: Vous avez deja une souscription active !");
        }

    }

    private function unSubscriptionBySMS()
    {
        $tools = new Utils();

        $subscr = new Subscriptions();
        $subscr->loadSubscription($this->get_msisdn());

        if($tools->asOnceATimeSubscribed($this->get_msisdn()) && $tools->unsuscribe($subscr->getId_subscr_n(), $subscr->getSubscr_msisdn_v(), $subscr->getSubscr_start_date_d(), $subscr->getSubscr_end_date_d()))
        {

            $SMS = "Votre desabonnement a ete effectue avec succes ! ";
            $tools->sendSMSMOOVKannel("GBICH SMS", $this->get_msisdn(), $SMS);

            // Logging ...
            $tools->logAction($this->get_msisdn(), 'UNSUSCRIBE', $subscr->getSubscr_formule_type_n(), 'SUCCESS');

            error_log("FUNCTION  unSubscriptionBySMS: SUBSCRIBER HAS LEFT SUCCESSFULLY... MSISDN -->" . $this->get_msisdn() . " !");

        }
        else
        {
            $SMS = "Impossible de vous desabonner automatiquement, veuillez envoyer SOS au 7070 afin que votre demande soit traitee manuellement. ";
            $tools->sendSMSMOOVKannel("GBICH SMS", $this->get_msisdn(), $SMS);

            // Logging ...
            $tools->logAction($this->get_msisdn(), 'UNSUSCRIBE', $subscr->getSubscr_formule_type_n(), 'FAILED');
            error_log("FUNCTION  unSubscriptionBySMS: ERROR SUBSCRIBER CANNOT LEFT ... MSISDN -->" . $this->get_msisdn() . " ----- ------");
        }
    }

    private function subscriptionAbort()
    {
        $USSDSender = new USSDSender();
        $USSDSender->load($this->get_screen_id(), $this->get_session_op(), utf8_encode($this->get_screen_text()), $this->get_options());
        $USSDSender->send();
    }

    private function SMSSyntaxError()
    {
        $sms = "Veuillez entrer la bonne syntaxe SVP. Tapez AIDE pour plus d'info !";
        $sms = utf8_encode($sms);

        $tools = new Utils();
        $tools->sendSMSMOOVKannel("7070", $this->get_msisdn(), $sms);

    }

    private function errorChoice()
    {
        $USSDSender = new USSDSender();
        $USSDSender->sendCustom("GBICH SMS: Tapez *707# à nouveau et entrez un choix correct (1, 2 ou 3). Sinon ... Zepaparaaa !!!");
    }

    private function billingProcess($msisdn)
    {
        return true;
    }

    private function billingProcessMOOVUCIP($msisdn, $amount, $ucip_url)
    {
        $numero = substr_replace($msisdn, "", 0, 3);
        $value = $amount;

        $TransId = mktime();
        $requete = '<?xml version="1.0"?>
        <methodCall><methodName>UpdateBalanceAndDate</methodName><params><param><value><struct>
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

    /* Behaviours ************************************************************* */
    private function loadDatasInMelody()
    {
        require_once('class_USSDServerDatas.php');

        // Loading Opencode's URI Requests ...
        $USSDServerDatas = new USSDServerDatas();
        $USSDServerDatas->getOpencodeURIRequests();

        // Common ...
        $this->set_msisdn($USSDServerDatas->get_msisdn());

        // USSD access ...
        $this->set_session_id($USSDServerDatas->get_session_id());
        $this->set_sc($USSDServerDatas->get_sc());
        $this->set_lang($USSDServerDatas->get_lang());
        $this->set_req_no($USSDServerDatas->get_req_no());
        $this->set_user_input($USSDServerDatas->get_user_input());
        $this->set_screen_id($USSDServerDatas->get_screen_id());

        // SMS access ...
        $this->setArrival($USSDServerDatas->getArrival());
        $this->setSMSContent($USSDServerDatas->getContent());

    }

    private function initialize()
    {
        require_once('class_Conn.php');
        require_once('class_Formules.php');
        require_once('class_Utils.php');


        require_once('class_USSDSender.php');
        require_once('class_Subscriptions.php');

        //Load Opencode URI Requests into Melody...
        $this->loadDatasInMelody();
    }

    /* Getters **************************************************************** */

    private function get_session_id()
    {
        return $this->_session_id;
    }

    private function get_sc()
    {
        return $this->_sc;
    }

    private function get_lang()
    {
        return $this->_lang;
    }

    private function get_msisdn()
    {
        return $this->_msisdn;
    }

    private function get_req_no()
    {
        return $this->_req_no;
    }

    private function get_screen_id()
    {
        return $this->_screen_id;
    }

    private function get_user_input()
    {
        return $this->_user_input;
    }

    private function get_session_op()
    {
        return $this->_session_op;
    }

    private function get_screen_text()
    {
        return $this->_screen_text;
    }

    private function get_options()
    {
        return $this->_options;
    }


    /* Setters **************************************************************** */

    private function set_session_id($value)
    {
        $this->_session_id = $value;
    }

    private function set_sc($value)
    {
        $this->_sc = $value;
    }

    private function set_lang($value)
    {
        $this->_lang = $value;
    }

    private function set_msisdn($value)
    {
        $this->_msisdn = $value;
    }

    private function set_req_no($value)
    {
        $this->_req_no = $value;
    }

    private function set_screen_id($value)
    {
        $this->_screen_id = $value;
    }

    private function set_user_input($value)
    {
        $this->_user_input = $value;
    }

    private function set_screen_text($value)
    {
        $this->_screen_text = $value;
    }

    private function set_session_op($value)
    {
        $this->_session_op = $value;
    }

    private function set_options($value)
    {
        $this->_options = $value;
    }


    /**
     * @return string
     */
    private function getArrival()
    {
        return $this->_arrival;
    }

    /**
     * @param string $arrival
     */
    private function setArrival($arrival)
    {
        $this->_arrival = $arrival;
    }

    /**
     * @return string
     */
    private function getSMSContent()
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    private function setSMSContent($content)
    {
        $this->_content = $content;
    }

}

