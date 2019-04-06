<?php
if(isset($_REQUEST['OPS']) && $_REQUEST['OPS'] == 'CUK'){
	echo checkUniqueAjax($_REQUEST['VAL'],$_REQUEST['TBL'],$_REQUEST['CLM']);
}
?>