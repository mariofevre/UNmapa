<?php

include_once("./_serverconfig/claveunica.php");

if (!isset($_SESSION['Unmapa'][$CU])){	
	$_SESSION['Unmapa'][$CU] = new ApplicationSettings();	
}


$Conec1 = new mysqli(
	$_SESSION['Unmapa'][$CU]->DATABASE_HOST, 
	$_SESSION['Unmapa'][$CU]->DATABASE_USERNAME, 
	$_SESSION['Unmapa'][$CU]->DATABASE_PASSWORD, 
	$_SESSION['Unmapa'][$CU]->DATABASE_NAME
);

$Conec1->set_charset('latin1');

if ($Conec1->connect_error) {
    die('Error de Conexión (' . $Conec1->connect_errno . ') '
            . $Conec1->connect_error);
}
?>
