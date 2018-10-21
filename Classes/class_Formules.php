<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 02/07/2018
 * Time: 17:35
 */

class Formules extends Conn {

    private $_id_formule;
    private $_formule_type;
    private $_formule_label;
    private $_formule_price;
    private $_formule_nbdays;
    private $_formule_bonusdays;

    private $_formule_notifbefore;


    public function __construct()
    {
    }

    public function loadFormule($id_formule)
    {
        $sqlScreen = "SELECT * FROM melody_sva_db.formules_tb form WHERE form.id_formule_n = '$id_formule'";

        $svaConn = $this->conn();

        $result = mysql_query($sqlScreen) or exit(mysql_error());

        if (mysql_num_rows($result))
        {
            if($ligne = mysql_fetch_array($result))
            {
                $this->setIdFormule($ligne['id_formule_n']);
                $this->setFormuleType($ligne['formule_type_v']);
                $this->setFormuleLabel($ligne['formule_label_v']);
                $this->setFormulePrice($ligne['formule_price_n']);
                $this->setFormuleNbdays($ligne['formule_nbdays_d']);
                $this->setFormuleBonusdays($ligne['formule_bonusdays_d']);
                $this->setFormuleNotifbefore($ligne['formule_notifbefore_n']);
            }
            else
                throw new Exception(mysql_error());
            mysql_free_result($result);

            //error_log("FORMULE CHOISIE : ".$id_formule);

        }

        mysql_close($svaConn);
    }

    /**
     * @return mixed
     */
    public function getIdFormule()
    {
        return $this->_id_formule;
    }

    /**
     * @param mixed $id_formule
     */
    private function setIdFormule($id_formule)
    {
        $this->_id_formule = $id_formule;
    }

    /**
     * @return mixed
     */
    public function getFormuleType()
    {
        return $this->_formule_type;
    }

    /**
     * @param mixed $formule_type
     */
    private function setFormuleType($formule_type)
    {
        $this->_formule_type = $formule_type;
    }

    /**
     * @return mixed
     */
    public function getFormuleLabel()
    {
        return $this->_formule_label;
    }

    /**
     * @param mixed $formule_label
     */
    private function setFormuleLabel($formule_label)
    {
        $this->_formule_label = $formule_label;
    }

    /**
     * @return mixed
     */
    public function getFormulePrice()
    {
        return $this->_formule_price;
    }

    /**
     * @param mixed $formule_price
     */
    private function setFormulePrice($formule_price)
    {
        $this->_formule_price = $formule_price;
    }

    /**
     * @return mixed
     */
    public function getFormuleNbdays()
    {
        return $this->_formule_nbdays;
    }

    /**
     * @param mixed $formule_nbdays
     */
    private function setFormuleNbdays($formule_nbdays)
    {
        $this->_formule_nbdays = $formule_nbdays;
    }

    /**
     * @return mixed
     */

    public function getFormuleBonusdays()
    {
        return $this->_formule_bonusdays;
    }

    /**
     * @param mixed $formule_bonusdays
     */
    private function setFormuleBonusdays($formule_bonusdays)
    {
        $this->_formule_bonusdays = $formule_bonusdays;
    }

    /**
     * @return mixed
     */
    public function getFormuleNotifbefore()
    {
        return $this->_formule_notifbefore;
    }

    /**
     * @param mixed $formule_notifbefore
     */
    private function setFormuleNotifbefore($formule_notifbefore)
    {
        $this->_formule_notifbefore = $formule_notifbefore;
    }

}