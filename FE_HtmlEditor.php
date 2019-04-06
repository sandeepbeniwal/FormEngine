
<?php 
/**
 * This File is create by sandeep beniwal 
 * File name:-	FormEngine.php
 */
//print_r($_SERVER);//[];
//str
$HTMLEditorPath = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],basename($_SERVER['PHP_SELF'])));
?>
<script type="text/javascript" src="<?php echo $HTMLEditorPath;?>tiny_mce/tiny_mce.js"></script>