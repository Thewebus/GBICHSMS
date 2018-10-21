<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 09/07/2018
 * Time: 20:44
 */

class SMS
{

    private $_rubr_id;
    private $_rubr_name;
    private $_rubr_ctnt_text;
    private $_rubr_ctnt_date_d;


    public function __construct()
    {
    }


    /**
     * @return mixed
     */
    public function getRubrName()
    {
        return $this->_rubr_name;
    }

    /**
     * @param mixed $rubr_name
     */
    public function setRubrName($rubr_name)
    {
        $this->_rubr_name = $rubr_name;
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
    public function setRubrCtntText($rubr_ctnt_text)
    {
        $this->_rubr_ctnt_text = $rubr_ctnt_text;
    }

    /**
     * @return mixed
     */
    public function getRubrId()
    {
        return $this->_rubr_id;
    }

    /**
     * @param mixed $rubr_id
     */
    public function setRubrId($rubr_id)
    {
        $this->_rubr_id = $rubr_id;
    }

    /**
     * @return mixed
     */
    public function getRubrCtntDateD()
    {
        return $this->_rubr_ctnt_date_d;
    }

    /**
     * @param mixed $rubr_ctnt_date_d
     */
    public function setRubrCtntDateD($rubr_ctnt_date_d)
    {
        $this->_rubr_ctnt_date_d = $rubr_ctnt_date_d;
    }

}