<?php
/**
 * Created by PhpStorm.
 * User: jacquesgoude
 * Date: 30/07/2018
 * Time: 14:03
 */
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


parse_str(substr(strstr($_SERVER['REQUEST_URI'], '?'), 1), $out);

if(isset($out['action']))
{

    if($out['action'] = "renewSubscriptions")
    {
        
        require_once('Classes/class_Utils.php');       
        $tools = new Utils();
        
        $subscriptionsToRenew = $tools->loadExpiredMSISDNToRenew();
        
        var_dump($subscriptionsToRenew);
    }


}
/**/
/**/
/**/