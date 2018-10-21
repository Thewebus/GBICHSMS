<?php
/**/
/**/
/**/
/**
 * **  Welcome to MELODY USSD Project ***********************
 *
 * @author 		: Jacques GOUDE

 * @name		: Melody 2.0
 * @since		: 13/07/2018
 * @version		: 2.0


 * @notes		:
 */


ini_set('max_execution_time', 300); // 5 minutes ...

require_once('Classes/class_MelodySMSSender.php');

parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?'), 1), $out);

if(isset($out['rubr']))
{
    $rubr = $out['rubr'];
    $gbichsms = new MelodySMSSender($rubr);
    $gbichsms->run();

}
/**/
/**/
/**/
