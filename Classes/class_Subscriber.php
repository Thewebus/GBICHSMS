<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 06/07/2018
 * Time: 10:12
 */

class Subscriber extends Conn
{
    private $_id_subscr;
    private $_subscr_quota;
    private $_subscr_quota_max;
    private $_subscr_msisdn;
    private $_subscr_notif_date;
    private $_subscr_status;


    /**
     * class_Subscriber constructor.
     * @param $_subscr_msisdn
     */

    public function __construct()
    {

    }

    public function setSubscriber($subscr_msisdn, $subscr_notif_date, $subscr_status)
    {
        $msg = "";

        $sql = 	"INSERT INTO `melody_sva_db`.`subscribers_tb`(`subscr_msisdn_v`,`subscr_notif_date_d`,`subscr_status_v`) VALUES ('$subscr_msisdn', '$subscr_notif_date', '$subscr_status')";

        $svaConn = $this->conn();

        if(mysql_query($sql))
        {
            error_log("AJOUT SOUSCRIPTEUR : ".$subscr_msisdn);

        }
        else exit(mysql_error());

        @mysql_close($svaConn);


        return $subscr_msisdn;
    }

    public function loadSubscriberDetails($_subscr_msisdn)
    {
        $sql = 	"SELECT * FROM melody_sva_db.subscribers_tb WHERE subscr_msisdn_v = '$_subscr_msisdn' ";

        $svaConn = $this->conn();

        $res = mysql_query($sql) or exit(mysql_error());

        if (mysql_num_rows($res))
        {
            if($ligne = mysql_fetch_array($res))
            {
                $this->setIdSubscr($ligne['id_subscr_n']);
                $this->setSubscrMsisdn($ligne['subscr_msisdn_v']);
                $this->setSubscrQuota($ligne['subscr_quota_n']);
                $this->setSubscrQuotaMax($ligne['subscr_quota_max_n']);
                $this->setSubscrNotifDate($ligne['subscr_notif_date_d']);
                $this->setSubscrStatus($ligne['subscr_status_v']);
            }

            @mysql_free_result($res);
        }
        @mysql_close($svaConn);

    }


    /**
     * @return mixed
     */
    public function getSubscrMsisdn()
    {
        return $this->_subscr_msisdn;
    }

    /**
     * @param mixed $subscr_msisdn
     */
    public function setSubscrMsisdn($subscr_msisdn)
    {
        $this->_subscr_msisdn = $subscr_msisdn;
    }

    
    /**
     * @return mixed
     */
    public function getSubscrNotifDate()
    {
        return $this->_subscr_notif_date;
    }

    /**
     * @param mixed $subscr_notif_date
     */
    public function setSubscrNotifDate($subscr_notif_date)
    {
        $this->_subscr_notif_date = $subscr_notif_date;
    }

    /**
     * @return mixed
     */
    public function getSubscrStatus()
    {
        return $this->_subscr_status;
    }

    /**
     * @param mixed $subscr_status
     */
    public function setSubscrStatus($subscr_status)
    {
        $this->_subscr_status = $subscr_status;
    }

    /**
     * @return mixed
     */
    public function getIdSubscr()
    {
        return $this->_id_subscr;
    }

    /**
     * @param mixed $id_subscr
     */
    public function setIdSubscr($id_subscr)
    {
        $this->_id_subscr = $id_subscr;
    }

    /**
     * @return mixed
     */
    public function getSubscrQuota()
    {
        return $this->_subscr_quota;
    }

    /**
     * @param mixed $subscr_quota
     */
    public function setSubscrQuota($subscr_quota)
    {
        $this->_subscr_quota = $subscr_quota;
    }

    /**
     * @return mixed
     */
    public function getSubscrQuotaMax()
    {
        return $this->_subscr_quota_max;
    }

    /**
     * @param mixed $subscr_quota_max
     */
    public function setSubscrQuotaMax($subscr_quota_max)
    {
        $this->_subscr_quota_max = $subscr_quota_max;
    }


}