<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 03/07/2018
 * Time: 10:11
 */

class Subscriptions extends Conn
{
    private $_id_subscr_n;
    private $_subscr_msisdn_v;
    private $_subscr_formule_type_n;
    private $_subscr_start_date_d;
    private $_subscr_end_date_d;
    private $_subscr_status_n;


    public function __construct()
    {
    }

    public function loadSubscription($_subscr_msisdn_v)
    {

        $sql = "SELECT * FROM melody_sva_db.subscriptions_tb WHERE subscr_msisdn_v = '$_subscr_msisdn_v' ";

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if (mysql_num_rows($res)) {

            if ($ligne = mysql_fetch_array($res)) {

                $this->setId_subscr_n($ligne['id_subscr_n']);
                $this->setSubscr_msisdn_v($ligne['subscr_msisdn_v']);
                $this->setSubscr_formule_type_n($ligne['subscr_formule_type_n']);
                $this->setSubscr_start_date_d($ligne['subscr_start_date_d']);
                $this->setSubscr_end_date_d($ligne['subscr_end_date_d']);
                $this->setSubscr_status_n($ligne['subscr_status_n']);
            }

            @mysql_free_result($res);
        }
        @mysql_close($svaConn);

    }

    public function getId_subscr_n()
    {
        return $this->_id_subscr_n;
    }

    public function setId_subscr_n($_id_subscr_n)
    {
        $this->_id_subscr_n = $_id_subscr_n;
    }

    public function getSubscr_msisdn_v()
    {
        return $this->_subscr_msisdn_v;
    }

    public function setSubscr_msisdn_v($_subscr_msisdn_v)
    {
        $this->_subscr_msisdn_v = $_subscr_msisdn_v;
    }

    public function getSubscr_formule_type_n()
    {
        return $this->_subscr_formule_type_n;
    }

    public function setSubscr_formule_type_n($_subscr_formule_type_n)
    {
        $this->_subscr_formule_type_n = $_subscr_formule_type_n;
    }

    public function getSubscr_start_date_d()
    {
        return $this->_subscr_start_date_d;
    }

    public function setSubscr_start_date_d($_subscr_start_date_d)
    {
        $this->_subscr_start_date_d = $_subscr_start_date_d;
    }

    public function getSubscr_end_date_d()
    {
        return $this->_subscr_end_date_d;
    }

    public function setSubscr_end_date_d($_subscr_end_date_d)
    {
        $this->_subscr_end_date_d = $_subscr_end_date_d;
    }

    public function getSubscr_status_n()
    {
        return $this->_subscr_status_n;
    }

    public function setSubscr_status_n($_subscr_status_n)
    {
        $this->_subscr_status_n = $_subscr_status_n;
    }


}