<?php 

	header("Cache-control: private");
	
	include('./_serverconfig/settings.php');
	
	session_start();
	
	include('./includes/conexionesmysql.php');
  
  /*
  if($_SESSION['is_open'] != 'TRUE'){
	  session_start();  
	  $_SESSION['is_open'] = 'TRUE';
	  include('./includes/conexionesmysql.php');
  }*/
?>
