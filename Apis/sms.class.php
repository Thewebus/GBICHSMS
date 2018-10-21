<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 05/07/2018
 * Time: 10:54
 */
header('Content-Type: text/html;charset=UTF-8');
include_once('smpp.class.php');
class SmsManager
{
    private $_smpp;
    private $_url;
    private $_port;
    private $_login;
    private $_pwd;

    public function __construct(){

        $this->_smpp = new MOOVSMPP();
        $this->_url='10.180.16.15';
        $this->_port=2775;
        $this->_login='gomedia';
        $this->_pwd='mediago';
        $this->_smpp->debug=1;

    }

    public function sendSms($emetteur,$destinataire,$message){
        $message=preg_replace('#â#','â  ',$message);
        $message = iconv('UTF-8','UTF-16BE',$message);
        $this->_smpp->open($this->_url,$this->_port,$this->_login,$this->_pwd);
        $rslt=$this->_smpp->send_long($emetteur,$destinataire,$message,true);
        return true;
    }

}