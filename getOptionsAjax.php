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
	$sql = "";
	if(getValueGPC('elementName') == "locations"){
		$sql = "select LOCATIONS_ID,LOCATION_NAME from locations where ACTIVE = 'Y' ";
		if(getValueGPC('catId') != ""){
			$sql .= " and COUNTRY_ID = ".getValueGPC('catId');
		}
	}
	echo queryToHTMLOptions($sql,'',true);
?>