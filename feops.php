<?php
if(file_exists("lib/include.php")) { 
	include_once("lib/include.php"); 
} else { 
	include_once("lib/db.class.php");
	include_once("lib/config.class.php");
	include_once("LoginProcess.php");
}
global $db_name;
$user_details = authentication($_GET['checksum']);
include_once('FormEngine.php');
include_once('FormEngine/FE_Controller.php');
?>