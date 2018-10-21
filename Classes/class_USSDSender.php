<?php

/**
*
* @author Jacques GOUDE
* @since 2.0
*/


class USSDSender extends utils {

	private $_screen_id;
	private $_session_op;
	private $_screen_text;
	private $_options = array();

	public function __construct()
	{
	}

	// Methods ****************************************************************/
	
	public function load($_screen_id, $_session_op, $_screen_text, $_options)
	{
		$this->set_screen_id($_screen_id);
		$this->set_session_op($_session_op);
		$this->set_screen_text($_screen_text);
		$this->set_options($_options);
	}
	
	public function send()
	{
		$xml  = '<?xml version="1.0"?>';
		$xml .= '<response>';
		$xml .= '<screen_type>menu</screen_type>';
		$xml .= '<text>'.$this->get_screen_text().'</text>';

		$xml .= '<options>';

		foreach($this->get_options() as $variable => $value)
		{
			$xml .= '<option choice="'.($variable+1).' : ">'.($value).'</option>';
		}

		$xml .= '</options>';

		$xml .= '<session_op>'.$this->get_session_op().'</session_op>';
		$xml .= '<screen_id>'.$this->get_screen_id().'</screen_id>';
		$xml .= '</response>';

		//error_log($xml);
		
		echo $xml;
		
	}

    public function sendCustom($textToShow)
    {
        $xml  = '<?xml version="1.0"?>';
        $xml .= '<response>';
        $xml .= '<screen_type>menu</screen_type>';
        $xml .= '<text>'.$textToShow.'</text>';

        $xml .= '<session_op>end</session_op>';
        $xml .= '<screen_id>'.$this->get_screen_id().'</screen_id>';
        $xml .= '</response>';
        
        echo $xml;

    }

	/* Getters **************************************************************** */
    
	private function get_session_id()
    {
        return $this->_session_id;
    }

    private function get_sc()
    {
        return $this->_sc;
    }
	
	private function get_lang()
    {
        return $this->_lang;
    }

    private function get_msisdn()
    {
        return $this->_msisdn;
    }

    private function get_req_no()
    {
        return $this->_req_no;
    }
	
	private function get_screen_id()
    {
        return $this->_screen_id;
    }

    private function get_user_input()
    {
        return $this->_user_input;
    }

    private function get_session_op()
    {
        return $this->_session_op;
    }
	
	private function get_screen_text()
    {
        return $this->_screen_text;
    }
	
	private function get_options()
	{
		return $this->_options;
	}

    private function get_fees()
    {
        return $this->_fees;
    }

    /* Setters **************************************************************** */
    
	private function set_session_id($value)
    {
        $this->_session_id = $value;
    }

    private function set_sc($value)
    {
        $this->_sc = $value;
    }
	
	private function set_lang($value)
    {
        $this->_lang = $value;
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

    private function set_user_input($value)
    {
        $this->_user_input = $value;
    }
	
	private function set_screen_text($value)
    {
        $this->_screen_text = $value;
    }

    private function set_session_op($value)
    {
        $this->_session_op = $value;
    }
	
	private function set_options($value)
	{
		$this->_options = $value;
	}
}

?>