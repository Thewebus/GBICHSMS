<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 12/07/2018
 * Time: 03:12
 */

class Distribution extends Conn
{
    private $_id_distrib_n;
    private $_distrib_date_d;
    private $_rubr_ctn_id_n;
    private $_id_subscr_n;
    private $_subscr_msisdn_v;

    /**
     * class_distribution constructor.
     * @param $_id_distrib_n
     */
    public function __construct()
    {

    }

    public function hasBeenAlreadyDelivered($_rubr_ctn_id_n, $_subscr_msisdn_v)
    {
        $return = FALSE;

        $sql = 	"SELECT * FROM melody_sva_db.distributions_tb WHERE rubr_ctn_id_n = '$_rubr_ctn_id_n' AND subscr_msisdn_v = '$_subscr_msisdn_v' ";

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if (mysql_num_rows($res))
        {
            $return = TRUE;
        }
        @mysql_close($svaConn);

        return $return;
    }

    public function setDistribution($_rubr_ctn_id_n, $_subscr_msisdn_v)
    {
        $return = FALSE;

        $_date_actual_timestamp   = time(); // Heure système ...
        $date_actual = date('Y-m-d H:i:s', $_date_actual_timestamp);

        $sql = 	"INSERT INTO `melody_sva_db`.`distributions_tb` (`distrib_date_d`, `rubr_ctn_id_n`,`subscr_msisdn_v`) VALUES ('$date_actual', '$_rubr_ctn_id_n', '$_subscr_msisdn_v')";

        //error_log("DISTRIBUTION SET FOR ------------>>> ".$sql);

        $svaConn = $this->conn();

        if(mysql_query($sql))
        {
            $return = TRUE;
        }
        else exit(mysql_error());

        @mysql_close($svaConn);

        return $return;

    }


    /**
     * @return mixed
     */
    public function getIdDistribN()
    {
        return $this->_id_distrib_n;
    }

    /**
     * @param mixed $id_distrib_n
     */
    public function setIdDistribN($id_distrib_n)
    {
        $this->_id_distrib_n = $id_distrib_n;
    }

    /**
     * @return mixed
     */
    public function getDistribDateD()
    {
        return $this->_distrib_date_d;
    }

    /**
     * @param mixed $distrib_date_d
     */
    public function setDistribDateD($distrib_date_d)
    {
        $this->_distrib_date_d = $distrib_date_d;
    }

    /**
     * @return mixed
     */
    public function getRubrCtnIdN()
    {
        return $this->_rubr_ctn_id_n;
    }

    /**
     * @param mixed $rubr_ctn_id_n
     */
    public function setRubrCtnIdN($rubr_ctn_id_n)
    {
        $this->_rubr_ctn_id_n = $rubr_ctn_id_n;
    }

    /**
     * @return mixed
     */
    public function getIdSubscrN()
    {
        return $this->_id_subscr_n;
    }

    /**
     * @param mixed $id_subscr_n
     */
    public function setIdSubscrN($id_subscr_n)
    {
        $this->_id_subscr_n = $id_subscr_n;
    }

    /**
     * @return mixed
     */
    public function getSubscrMsisdnV()
    {
        return $this->_subscr_msisdn_v;
    }

    /**
     * @param mixed $subscr_msisdn_v
     */
    public function setSubscrMsisdnV($subscr_msisdn_v)
    {
        $this->_subscr_msisdn_v = $subscr_msisdn_v;
    }

}