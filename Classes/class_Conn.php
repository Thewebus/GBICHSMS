<?php

class Conn {
	
  private $_hostname = "localhost";
  private $_username = "root";
  private $_password = "Gbich@20!8_1#";
  private $_database = "melody_sva_db";

  public function __construct()
  {
  }

  public function conn_DEF()
  {
    $conn = mysql_connect($this->get_hostname(), $this->get_username(), $this->get_password()) or trigger_error(mysql_error(), E_USER_ERROR);
    $conn_db = mysql_select_db($this->get_database(), $conn);

    return $conn;
  }

  public function conn()
  {
    $conn = mysql_connect($this->get_hostname(), $this->get_username(), $this->get_password()) or trigger_error(mysql_error(), E_USER_ERROR);
    $conn_db = mysql_select_db($this->get_database(), $conn);

    return $conn;
  }

  public function oci_connect_db()
  {
    $conn = oci_connect("ABILLITY_PROD", "ABILLITY_PROD", "//192.168.30.161:1521/ABLORCL") or exit(var_dump(oci_error()));
  	//$conn = oci_connect("ABILLITY_PROD", "ABILLITY_PROD", "//192.168.2.222:1521/IVRCL") or exit(var_dump(oci_error()));
    return $conn;
  }

  /* GETTERS ****************************************************************/
  public function get_hostname()
  {
    return $this->_hostname;
  }

  public function get_username()
  {
    return $this->_username;
  }

  public function get_password()
  {
    return $this->_password;
  }

  public function get_database()
  {
    return $this->_database;
  }

  /* SETTERS ****************************************************************/
  public function set_hostname($value)
  {
    $this->_hostname = $value;
  }

  public function set_username($value)
  {
    $this->_username = $value;
  }

  public function set_password($value)
  {
    $this->_password = $value;
  }

  public function set_database($value)
  {
    $this->_database = $value;
  }
}

?>