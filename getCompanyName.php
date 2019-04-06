<?php
	if(file_exists("lib/include.php")) { 
		include_once("lib/include.php"); 
	} else { 
		include_once("lib/db.class.php");
		include_once("lib/config.class.php");
		include_once("LoginProcess.php");
	}
		global $db_name;
		//$user_details = authentication($_GET['checksum']);
		$HTML='';
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name);
		@mysql_select_db($Config->db_name);
	global $FORM_ERROR;
	include_once('FormEngine.php');
	global $PageName;
	$PageName = 'getCompanyName.php';
	include_once('customer.class.php');
	$customer = new customer("",false, false , false); 
	echo generateSuggessionJSON($customer->searchOperation("CUSTOMER_ID,COMPANY_NAME,COMPANY_NAME",getValueGPC('token')),0,'Edit',0,$PageName);
?>