<?php

/**
 * @author Tom Gillespy
 * @copyright 2014
 */

require('radec.class.php');

$radec = new radec(radec::decimaldegrees(51, 49, 48), -radec::decimaldegrees(0, 49, 48));

$radec->setradec((radec::decimaldegrees(9, 27, 52.9)), radec::decimaldegrees(14, 57, 6.5));

//$calculationtime = strtotime('1998-08-10 23:10:00');
//$calculationtime = strtotime('now');
$calculationtime = microtime(true);
echo gmdate('r', $calculationtime);
echo "\r\n";
echo 'UT: '.$radec->getdecimalUT($calculationtime)."\r\n";
echo 'Days from J2000: '.$radec->daysbeforeJ2000($calculationtime)."\r\n";
echo 'LST: '.$radec->getLST($calculationtime)."\r\n";
echo 'HA: '.$radec->getHA($calculationtime)."\r\n";
echo "\r\n";


echo 'Alt: '.number_format($radec->getALT($calculationtime), 2)."\r\n";
echo 'Az: '.number_format($radec->getAZ($calculationtime), 2)."\r\n";




?>