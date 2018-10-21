<?php

class USSDService {

	private $_svc_id;
	private $_svc_code;
	private $_svc_desc;
	private $_svc_status;
	private $_svc_amount;
	private $_svc_livrableURL;
	private $_svc_mode;

	private $_svc_confirm_text1;

	public function __construct()
	{
	}

	//Getters *****************************************************************/
	public function get_svcID()
	{
		return $this->_svc_id;
	}

	public function get_svcCode()
	{
		return $this->_svc_code;
	}

	public function get_svcDesc()
	{
		return $this->_svc_desc;
	}

	public function get_svcStatus()
	{
		return $this->_svc_status;
	}

	public function get_svcAmount()
	{
		return $this->_svc_amount;
	}

	public function get_svcLivrURL()
	{
		return $this->_svc_livrableURL;
	}

	public function get_svcMode()
	{
		return $this->_svc_mode;
	}

	public function get_svcConfirmText1()
	{
		return $this->_svc_confirm_text1;
	}

	//Setters *****************************************************************/
	public function set_svcID($value)
	{
		$this->_svc_id = $value;
	}

	public function set_svcCode($value)
	{
		$this->_svc_code = $value;
	}

	public function set_svcDesc($value)
	{
		$this->_svc_desc = $value;
	}

	public function set_svcStatus($value)
	{
		$this->_svc_status = $value;
	}

	public function set_svcAmount($value)
	{
		$this->_svc_amount = $value;
	}

	public function set_svclivrURL($value)
	{
		$this->_svc_livrableURL = $value;
	}

	public function set_svcMode($value)
	{
		$this->_svc_mode = $value;
	}

	public function set_svcConfirmText1($value)
	{
		$this->_svc_confirm_text1 = $value;
	}

}

?>