<?php
global $FE_UploadPath ;
if($_SERVER['HTTP_HOST'] != 'localhost'){
	$FE_UploadPath = "FormEngine/usercontent/";
}else{
	$FE_UploadPath = "/Applications/MAMP/htdocs/GarsInfo/cache/";
}

?>