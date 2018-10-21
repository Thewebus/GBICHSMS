<?php

/**
 *
 * @author Jacques GOUDE
 * @version 2.0
 */

class USSDBDDatas extends Conn
{

    private $_screen_id;
    private $_session_op;
    private $_screen_text;

    private $_options = array();

    public function __construct()
    {
    }

    // Methods ****************************************************************/

    public function getDBContents($screen_id, $user_input)
    {
        $return = FALSE;

        $sqlScreen = "SELECT * FROM melody_sva_db.screen_tb WHERE last_screen_id_n = '$screen_id' AND user_input_v  = '$user_input' ";

        $svaConn = $this->conn();

        $resScreen = mysql_query($sqlScreen) or exit(mysql_error());

        if(mysql_num_rows($resScreen))
        {
            // Menu principal ...

            if($ligne = mysql_fetch_array($resScreen))
            {
                $this->set_screen_id($ligne['id_screen_n']);
                $this->set_session_op($ligne['session_op_t']);
                $this->set_screen_text($ligne['screen_text_t']);
            }
            else
            {
                throw new Exception(mysql_error());
            }
            mysql_free_result($resScreen);

            // Options ...

            $scrid = $this->get_screen_id();

            $sqlScreenOptions = "SELECT * FROM melody_sva_db.screen_content_tb  WHERE id_scr_parent_n = '$scrid' ORDER BY content_rank_n ASC";

            $resScreenOptions = mysql_query($sqlScreenOptions) or exit(mysql_error());

            if(mysql_num_rows($resScreenOptions))
            {
                while ($ligne = mysql_fetch_array($resScreenOptions))
                {
                    $this->set_options($ligne['content_to_show_v']);
                }
                @mysql_free_result($resScreenOptions);
            }

            $return = TRUE;
        }

        @mysql_close($svaConn);

        return $return;
    }

    // Getters ****************************************************************/
    public function get_screen_id()
    {
        return $this->_screen_id;
    }

    public function get_session_op()
    {
        return $this->_session_op;
    }

    public function get_screen_text()
    {
        return $this->_screen_text;
    }

    public function get_options()
    {
        return $this->_options;
    }


    // Setters ****************************************************************/
    private function set_screen_id($value)
    {
        $this->_screen_id = $value;
    }

    private function set_session_op($value)
    {
        $this->_session_op = $value;
    }

    private function set_screen_text($value)
    {
        $this->_screen_text = $value;
    }

    private function set_options($value)
    {
        $this->_options[] = $value;
    }

}

?>