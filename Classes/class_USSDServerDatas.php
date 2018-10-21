<?php

/**
 *
 * @author Jacques GOUDE
 * @version 2.0
 */

class USSDServerDatas extends Utils
{

    private $_sc = "";
    private $_lang = "";
    private $_msisdn = "";
    private $_req_no = "";
    private $_session_id = "";
    private $_screen_id = "";
    private $_user_input = "";

    private $_arrival = "";
    private $_content = "";


    public function __construct()
    {
    }


    // Methods *****************************************************************/
    public function getOpencodeURIRequests()
    {
        parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?'), 1), $out);

        if(isset($out['sc']))
        {
            $this->set_sc($out['sc']);
            $this->set_lang($out['lang']);
            $this->set_msisdn($out['msisdn']);
            $this->set_req_no($out['req_no']);
            $this->set_session_id($out['session_id']);
            $this->set_screen_id($out['screen_id']);
            $this->set_user_input($out['user_input']);

            //error_log("ACCESS BY USSD ... MSISDN : " . $this->get_msisdn());

        }


        if(isset($out['srcAddress']))
        {
            $this->set_msisdn(str_replace("+", "", $out['srcAddress']));
            $this->setArrival($out['arrival']);
            $this->setContent($out['content']);

            error_log("ACCESS BY SMSC ... MSISDN : " . $this->get_msisdn() . " ... content : " . $this->getContent());
        }

    }

	
    // Setters *****************************************************************/

    private function set_session_id($value)
    {
        $this->_session_id = $value;
    }

    private function set_sc($value)
    {
        $this->_sc = $value;
    }

    private function set_user_input($value)
    {
        $this->_user_input = $value;
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

    private function set_lang($value)
    {
        $this->_lang = $value;
    }


    // Getters *****************************************************************/

    public function get_session_id()
    {
        return $this->_session_id;
    }

    public function get_sc()
    {
        return $this->_sc;
    }

    public function get_user_input()
    {
        return $this->_user_input;
    }

    public function get_msisdn()
    {
        return $this->_msisdn;
    }

    public function get_req_no()
    {
        return $this->_req_no;
    }

    public function get_screen_id()
    {
        return $this->_screen_id;
    }

    public function get_lang()
    {
        return $this->_lang;
    }


    /**
     * @return mixed
     */
    public function getArrival()
    {
        return $this->_arrival;
    }

    /**
     * @param mixed $arrival
     */
    public function setArrival($arrival)
    {
        $this->_arrival = $arrival;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

}

