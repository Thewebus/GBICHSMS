<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 08/08/2018
 * Time: 17:31
 */

class MelodySubscribersManager
{

    private $_action;
    private $_counterMax;

    private $_url_1;
    private $_url_2;

    private $_transID;


    public function __construct($_classAction)
    {
        $this->initialize($_classAction);
    }


    /**
     * @throws Exception
     */
    public function run()
    {
        $tools = new Utils();
        $formule = new Formules();

        $actionDateTime = date('dmY-His', time());

        //TODO: Test this code portion .... (to confirm ...)

        switch ($this->getAction()) {

            case 'renewSubscriptions':

                $tools->writeLog("START CHECKING SUBSCRIBERS TO RENEW ...", "renewSubscriptions.log");

                if ($subscriptionsToRenew = $tools->loadExpiredMSISDNToRenew()) {

                    $counter    = $this->getCounterMax();
                    $counterMax = $this->getCounterMax();

                    $billingLimit = 0;

                    $maxToBillToday = 4000;

                    $totalExpiredMSISDN = sizeof($subscriptionsToRenew);

                    $tools->writeLog($totalExpiredMSISDN . " SUBSCRIBERS TO RENEW FOUNDED ... START LOOPING THROUGH ... $totalExpiredMSISDN", "renewSubscriptions.log");

                    $SMS = "FUNCTION renewSubscriptions: START LOOPING THROUGH $totalExpiredMSISDN ($maxToBillToday checks left).";
                    $tools->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $SMS);

                    $time_start = $tools->microtime_float();

                    foreach ($subscriptionsToRenew as $subscr) {
                        //if ($subscr->getSubscr_msisdn_v() === '22573485669') // Tests ...
                        //{
                        if ($counter >= 0) {

                            if($billingLimit == $maxToBillToday)
                            {
                                $tools->writeLog($billingLimit." SUSCRIBERS BILLING LIMIT REACHED ... BILLING STOPPED !", "renewSubscriptions.log");

                                break;
                            }


                            $currentMSISDN = $subscr->getSubscr_msisdn_v();
                            $currentFormuleType = $subscr->getSubscr_formule_type_n();

                            //$tools->writeLog("TRYING RENEWING MSISDN -------->> $currentMSISDN : OVER $totalExpiredMSISDN ... ($counter / $counterMax)", "renewSubscriptions.log");

                            //$tools->writeLog($billingLimit." SUSCRIBERS BILLED - current: $currentMSISDN - formule: $currentFormuleType - still checking ...", "renewSubscriptions.log");

                            $transID = (mktime() + $currentMSISDN);

                            $formule->loadFormule($currentFormuleType);

                            if ($tools->updateBalanceAndDateProcessMOOVUCIP($currentMSISDN, (-($formule->getFormulePrice())), $this->getUrl1(), $transID) && $tools->updateSubscriptionbyUSSD($subscr)) {

                                $tools->billingSuccessActions($currentMSISDN, 'BILLING', $formule->getFormuleType(), 'SUCCESS', $formule->getFormulePrice(), $transID);

                                $billingLimit++;

                                $tools->writeLog($billingLimit." SUSCRIBERS BILLED - current: $currentMSISDN - formule: $currentFormuleType - still checking ...", "renewSubscriptions.log");

                            } elseif ($tools->updateBalanceAndDateProcessMOOVUCIP($currentMSISDN, (-($formule->getFormulePrice())), $this->getUrl2(), $transID) && $tools->updateSubscriptionbyUSSD($subscr)) {

                                $tools->billingSuccessActions($currentMSISDN, 'BILLING', $formule->getFormuleType(), 'SUCCESS', $formule->getFormulePrice(), $transID);

                                $billingLimit++;

                                $tools->writeLog($billingLimit." SUSCRIBERS BILLED - current: $currentMSISDN - formule: $currentFormuleType - still checking ...", "renewSubscriptions.log");

                            } else {

                                $this->billingFailureActions($currentMSISDN, 'BILLING', $formule->getFormuleType(), 'FAILED', $formule->getFormulePrice(), $transID);
                            }

                        } else {

                            $tools->writeLog("MAX BILL LIMIT REACHED !!!", "renewSubscriptions.log");

                            break;
                        }
                        $counter = ($counter - 1);
                        // }

                    }

                    $time_end = $tools->microtime_float();
                    $time = $time_end - $time_start;

                    $tools->writeLog("ENDING LOOPING ****************************************** ... MSISDN BILLED - TIME SPENT (SECONDS): $time ! ", "renewSubscriptions.log");

                    $SMS = "FUNCTION renewSubscriptions: END LOOPING OVER $totalExpiredMSISDN - MSISDN BILLED: $billingLimit  - TIME SPENT: ".($time/60)." !";
                    $tools->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $SMS);


                } else {

                    $SMS = "FUNCTION renewSubscriptions: NO SUBSCRIPTIONS TO RENEW ... ENDING.";
                    $tools->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $SMS);

                    $tools->writeLog("NO SUBSCRIBERS TO RENEW FOUNDED ... ENDING PROCESS !", "renewSubscriptions.log");
                }

                break;

            case 'alertBeforeRenewing':


                if ($MSISDNsToAlert = $tools->loadMSISDNToAlert()) {

                    $totalMSISDNToAlert = sizeof($MSISDNsToAlert);

                    $SMS = "FUNCTION alertBeforeRenewing: START SENDIND SMS TO  ... $totalMSISDNToAlert MSISDN.";
                    $tools->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $SMS);

                    $time_start = $tools->microtime_float();

                    foreach ($MSISDNsToAlert as $subscr) {

                        $currentMSISDN = $subscr->getSubscr_msisdn_v();

                        $SMS = "Cher abonne(e), votre souscription expire aujourd'hui et sera reconduite automatiquement demain. Pour desouscrire, envoyez STOP par SMS au 7070 !";
                        $tools->sendSMSMOOVKannel("GBICH SMS", $currentMSISDN, $SMS);

                    }

                    $time_end = $tools->microtime_float();
                    $time = $time_end - $time_start;

                    $tools->writeLog("ENDING LOOPING ****************************************** ... TIME SPENT: " . $time, "alertBeforeRenewing.log");

                    $SMS = "FUNCTION alertBeforeRenewing: END SENDIND SMS TO  ... $totalMSISDNToAlert MSISDN.";
                    $tools->sendSMSMOOVKannel("GBICH DEBUG", '22552390202', $SMS);

                } else {
                    $tools->writeLog("NO SUBSCRIBERS TO ALERT FOUNDED ... ENDING PROCESS !", "alertBeforeRenewing.log");
                }

                break;

            default:
        }
    }


    /* InnerProcess *********************************************************** */

    private function billingSuccessActions($msisdn, $log_type, $log_desc, $log_status, $amount, $transID)
    {

        $tools = new Utils();
        $thisSubscr = new Subscriptions();
        $thisSubscr->loadSubscription($msisdn);

        $_date_deb = $thisSubscr->getSubscr_start_date_d();
        $_date_fin = $thisSubscr->getSubscr_end_date_d();

        // Logging ...
        $tools->logAction($msisdn, $log_type, $log_desc, $log_status, (-($amount)), $amount, $transID);


        // Send SMS ...
        $SMS = "ZEPAPARAAA !! Souscription $log_desc reconduite avec succes, valable du $_date_deb au $_date_fin !";
        $tools->sendSMSMOOVKannel("GBICH SMS", $msisdn, $SMS);

        $msg = " ******* BILLING SUBSCRIBER :  SUCCEDED ... ACTION ($log_type $log_desc)  FOR THIS MSISDN ----> " . $msisdn;
        $tools->writeLog($msg, "renewSubscriptions.log");

        error_log($msg);

    }

    private function billingFailureActions($msisdn, $log_type, $log_desc, $log_status, $amount, $transID)
    {
        $tools = new Utils();

        // Logging ...
        $tools->logAction($msisdn, $log_type, $log_desc, $log_status, (-($amount)), $amount, $transID);

        // Send SMS ...
        //$SMS = "ZEPAPARAAA !! Credit insuffisant pour le reabonnement automatique a ta souscription $log_desc . Recharge viiite dehh !";
        //$tools->sendSMSMOOVKannel("GBICH SMS", $msisdn, $SMS);

        //error_log("######### ******* BILLING SUBSCRIBER :  FAILED ... ACTION ($log_type $log_desc)  FOR THIS MSISDN ----> " . $msisdn);

    }


    /* Initialize *********************************************************** */

    private function initialize($_classAction)
    {
        require_once('class_Conn.php');
        require_once('class_Utils.php');
        require_once('class_Formules.php');
        require_once('class_Subscriptions.php');

        $tools = new Utils();

        $this->setCounterMax($tools->loadBillingCountLeftOfThisDay());

        $this->setUrl1('http://10.184.38.10:10010/Air');
        $this->setUrl2('http://10.184.38.11:10010/Air');

        $this->setTransID(mktime());

        $this->setAction($_classAction);

    }


    /* Set & Get  *********************************************************** */

    /**
     * @return mixed
     */
    private function getAction()
    {
        return $this->_action;
    }

    /**
     * @param mixed $action
     */
    private function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * @return mixed
     */
    private function getCounterMax()
    {
        return $this->_counterMax;
    }

    /**
     * @param mixed $counterMax
     */
    private function setCounterMax($counterMax)
    {
        $this->_counterMax = $counterMax;
    }

    /**
     * @return mixed
     */
    private function getUrl1()
    {
        return $this->_url_1;
    }

    /**
     * @param mixed $url_1
     */
    private function setUrl1($url_1)
    {
        $this->_url_1 = $url_1;
    }

    /**
     * @return mixed
     */
    private function getUrl2()
    {
        return $this->_url_2;
    }

    /**
     * @param mixed $url_2
     */
    private function setUrl2($url_2)
    {
        $this->_url_2 = $url_2;
    }

    /**
     * @return mixed
     */
    private function getTransID()
    {
        return $this->_transID;
    }

    /**
     * @param mixed $transID
     */
    private function setTransID($transID)
    {
        $this->_transID = $transID;
    }

}

