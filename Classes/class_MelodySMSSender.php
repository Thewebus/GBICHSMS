<?php

/**
*
* @author Jacques GOUDE
* @since 2.0
*/

class MelodySMSSender {


    private $_rubr;

    private $_rubric_name;
    private $_rubr_ctnt_text;



    public function __construct($rubr)
    {
        $this->setRubr($rubr);
        $this->initialize();
    }

    public function run()
{
    try {

        // Initialisation des utilitaires ...
        $tools = new Utils();

        ///Loading the last SMS of this rubr ...
        $rubrSMSToSend  = $tools->loadGBICHSMS($this->getRubr());

        if ($rubrSMSToSend == FALSE)
        {
            //error_log("AUCUN SMS DISPONIBLE POUR LA RUBRIQUE (".$this->getRubr().") CE JOUR !");
        }
        else
        {
            $smsid          = $rubrSMSToSend->getRubrId();
            $smsString      = $rubrSMSToSend->getRubrCtntText();

            //Affichage du nom de la rubrique ...
            //$smsString = $rubrSMSToSend->getRubrName().': '.$rubrSMSToSend->getRubrCtntText();

            //Loading all valid subscribers ...
            $validSubscribersMSISDNObj = $tools->loadValidSubscribersMSISDN();

            //$distr = new Distribution();

            $time_start = $tools->microtime_float();

            error_log(" ****************** ***************** GBICH SMS STARTING DELIVERY ...". " RUBR: ".$this->getRubr(). " ... SMS ID =". $smsid);
            error_log("content is : ".$smsString);

            $currentMSISDN              = 0;
            $totalOfActiveSubscribers   = sizeof($validSubscribersMSISDNObj);

            foreach ($validSubscribersMSISDNObj as $subscrMSISDN => $msisdn)
            {
                // Distribution directe ...
                $tools->sendSMSMOOVKannel("GBICH SMS", $msisdn,utf8_encode($smsString));

                // $distr->setDistribution($smsid, $msisdn);

                $currentMSISDN++;

                error_log("DISTRIBUTION: SMS SENT TO MSISDN -----> ".$msisdn." ... SMS ID =". $smsid. " PROGRESSION : ". $currentMSISDN. " / ".$totalOfActiveSubscribers);


                // Fonctionnalité d'envoi de messages par intervalles de temps ... experimental ...
                /*
                if(!$distr->hasBeenAlreadyDelivered($smsid,$msisdn))
                {
                    $tools->sendSMSMOOVKannel("GBICH SMS", $msisdn,utf8_encode($smsString));
                    $distr->setDistribution($smsid, $msisdn);
                }
                else
                {
                    //error_log("SMS ALREADY DELIVERED TO MSISDN ".$msisdn." ... SKIPPING !");
                }
                */

            }

            $time_end = $tools->microtime_float();

            $time = $time_end - $time_start;

            error_log("GBICH SMS ***************************************************************************** END DELIVERY ... TIME SPENT: ". $time);
            error_log("Content was : ".$smsString);
        }

    }

    catch (Exception $e) {
        echo $e->getMessage();
    }
}

    /* InnerProcess *********************************************************** */

	/* Behaviours ************************************************************* */

    private function initialize()
    {
		require_once ('class_Conn.php');
		require_once ('class_Utils.php');
        require_once ('class_SMS.php');
        require_once ('class_Distribution.php');
    }

    /**
     * @return mixed
     */
    public function getRubricName()
    {
        return $this->_rubric_name;
    }

    /**
     * @param mixed $rubric_name
     */
    private function setRubricName($rubric_name)
    {
        $this->_rubric_name = $rubric_name;
    }

    /**
     * @return mixed
     */
    public function getRubrCtntText()
    {
        return $this->_rubr_ctnt_text;
    }

    /**
     * @param mixed $rubr_ctnt_text
     */
    private function setRubrCtntText($rubr_ctnt_text)
    {
        $this->_rubr_ctnt_text = $rubr_ctnt_text;
    }

    /**
     * @return mixed
     */
    public function getRubr()
    {
        return $this->_rubr;
    }

    /**
     * @param mixed $rubr
     */
    private function setRubr($rubr)
    {
        $this->_rubr = $rubr;
    }

}

