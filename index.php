<?php
/**/
/**/
/**/
/**
* **  Welcome to MELODY USSD Project ***********************
*
* @author 		: Jacques GOUDE
* @co-author     : Roland YAVO

* @name			: Melody 2.0  		
* @since		: 12/12/2013
* @update		: 22/06/2018		
* @version		: 2.0


* @notes		: With Opencode USSDC External Interface
*/

/**/
/**/

require_once('Classes/class_MelodyUSSD.php');

$gbichsms = new MelodyUSSD();
$gbichsms->run();

/**/
/**/
/**/
