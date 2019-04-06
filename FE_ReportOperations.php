<?php
include_once('FormEngine.php');
function queryToReport($_sql,$PARAMS = "",$isPaging = true,$isHorizontal = false,$reportHeader='',$indexRequired=0){
		//include_once("lib/db.class.php");
		//includeOnce("lib/config.class.php");
		includeOnce("lib/LoginProcess.php");
		$Array =array();

		global $db_name,$PageName,$isApiCall;
		$StartFrom = 0;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		//$Config->max_Page_Size = 10;
		if(getValueGPC('pageNumber') != "")
		$StartFrom = ((getValueGPC('pageNumber')*$Config->max_Page_Size)-$Config->max_Page_Size);
		else{
		$_GET['pageNumber'] = 1;
		}
		@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
		//echo $db_name;
		$sql=$_sql;//"select COUNTRY,NAME from COUNTRY order by NAME limit ";
		$sql_result=@mysql_query($sql);
		$totalRows = mysql_num_rows($sql_result);
		$totalPages= $totalRows/$Config->max_Page_Size;
		if(($totalRows%$Config->max_Page_Size) > 0){
			$totalPages++;
		}
		$sqlfinal = $sql;
		
		if(getValueGPC('print_report')=="true" || $totalPages == 1 ){
			$sqlfinal=$sql." LIMIT 0 , 20000";
		}else{
			if($isPaging){
				$sqlfinal=$sql." LIMIT $StartFrom , $Config->max_Page_Size";
			}
		}
		$sql_result=mysql_query($sqlfinal) or die($sql.mysql_error());
		$i=0;
		/*while($line = mysql_fetch_assoc($res)){
			$line;
			//$optionArray[$i]['VALUE']  = $line[0];
			//$optionArray[$i]['LABEL']  = $line[1];
			//++$i;
		}/**/
		$i = 1;
		$ApiArray = array();
		global $generatePageReport,$headerRepeatOnEvery;$reportPageCount = 0;
		while($line = mysql_fetch_assoc($sql_result))//$MySQLObject->Row())
		{
			$ReceivedProperties = $line;
			$ApiArray[sizeof($ApiArray)] = $ReceivedProperties;
			$j=0;
			foreach($ReceivedProperties as $key => $newvalue)
			{
				$Array[0][$j] = $key;
				$Array[$i][$j] = trim($newvalue);
				++$j;
			}
			$i++;
			if(@$headerRepeatOnEvery > 0){
				if($i%@$headerRepeatOnEvery == 0){
					if($isHorizontal){
						$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS,$isHorizontal);
					}else{
						$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS);
					}
					$reportPageCount++;
					$ApiArray = array();
					$i=0;
				}
			}else{ /* Noting to do here*/}
		}
		if($isHorizontal){
			$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS,$isHorizontal);
		}else{
			$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS);
		}
		
		
		$pagingCode = "";
		$printCode = "";
		if(getValueGPC('print_report')=="true" || getValueGPC('print_page')=="true"){
			if(getValueGPC('print_report_screen')==''){
				$printCode="<script>window.print();</script>";
			}
			$pagingCode="";
		}else{
			if($isPaging)
				$pagingCode =pagingCode($totalPages,$PARAMS);
		}
		//if($totalPages < 2){ $pagingCode = '';}
		if($isApiCall){
			//$output = fopen("php://output",'w') or die("Can't open php://output");
			//$filename = $PageName."_".date("Y-m-d_H-i",time());
			header('Content-type: text/json');
			//header("Content-Disposition:attachment;filename=\"$filename.json\"");
			header("Expires: 0");
    	    //header("Content-Transfer-Encoding: binary ");
			foreach($ApiArray as $k => $v){
				foreach($v as $_k => $_v){
					$ApiArray[$k][$_k] = addcslashes($_v);
				}
			}
        	print "$header";
			echo json_encode($ApiArray);
			return ;
			//echo $returnHtml;
		}
		if(getValueGPC('excel_export') == "json"){
			//$output = fopen("php://output",'w') or die("Can't open php://output");
			$filename = $PageName."_".date("Y-m-d_H-i",time());
			header('Content-type: text/json');
			header("Content-Disposition:attachment;filename=\"$filename.json\"");
			header("Expires: 0");
    	    header("Content-Transfer-Encoding: binary ");
        	print "$header";
			echo json_encode($ApiArray);
			die();
			//echo $returnHtml;
		}else if(getValueGPC('excel_export') == "xml"){
			//$output = fopen("php://output",'w') or die("Can't open php://output");
			$filename = $PageName."_".date("Y-m-d_H-i",time());
			header('Content-type: text/xml');
			header("Content-Disposition:attachment;filename=\"$filename.xml\"");
			header("Expires: 0");
    	    header("Content-Transfer-Encoding: binary ");
        	print "$header";
			$xml = new  SimpleXMLElement('<root/>');// $xml;
			$xml = array_to_xml($ApiArray,$xml);
			print $xml->asXML();
			die();
			//echo $returnHtml;
		}else if(getValueGPC('excel_export') == "csv"){
			$output = fopen("php://output",'w') or die("Can't open php://output");
			$filename = $PageName."_".date("Y-m-d_H-i",time());
			header("content-type:application/csv;charset=UTF-8");
			header("Content-Disposition:attachment;filename=\"$filename.csv\"");
			header("Expires: 0");
    	    header("Content-Transfer-Encoding: binary ");
        	print "$header";
			if(sizeof($reportHeader) > 0 ){
				foreach(@$reportHeader as $key){
					fputcsv($output, $key);
				}
			}
			foreach($Array as $key){
				fputcsv($output, $key);
			}die();
			//echo $returnHtml;
		}else if(getValueGPC('excel_export') == "true"){
			$filename = $PageName."_".date("Y-m-d_H-i",time());
        	header( "Content-type: application/vnd.ms-excel; charset=UTF-8");
	        header("Content-Disposition: attachment; filename=".$filename.".xls");
	        header("Expires: 0");
    	    header("Content-Transfer-Encoding: binary ");
        	print "$header";
			if(sizeof($reportHeader) > 0 ){
				echo "<table>";
				foreach(@$reportHeader as $key){
					if(is_array($key)){
						echo "<tr><td></td>";
						foreach($key as $k => $v){
							echo "<td colspan=5 align=\"center\">".$v."</td>";
						}
						echo "</tr>";
					}
				}
				echo "</table>";
			}
			echo $returnHtml;
			die();
		}else{
			//if($totalPages == 1){ $pagingCode = '';}
			return $pagingCode."<hr>".$returnHtml.$printCode;
		}
		//return $pagingCode."<hr>".$returnHtml.$printCode;
}
function queryToSortable($_sql,$TNAME,$COLUMN_NAME,$ORDER_COLUMN,$_columnOrder){
		//include_once("lib/db.class.php");
		//includeOnce("lib/config.class.php");
		includeOnce("lib/LoginProcess.php");
		$Array =array();

		global $db_name,$PageName,$isApiCall;
		$StartFrom = 0;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		//$Config->max_Page_Size = 10;
		if(getValueGPC('pageNumber') != "")
		$StartFrom = ((getValueGPC('pageNumber')*$Config->max_Page_Size)-$Config->max_Page_Size);
		else{
		$_GET['pageNumber'] = 1;
		}
		@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
		//echo $db_name;
		$sql=$_sql;//"select COUNTRY,NAME from COUNTRY order by NAME limit ";
		$sql_result=@mysql_query($sql);
		$totalRows = mysql_num_rows($sql_result);
		$totalPages= $totalRows/$Config->max_Page_Size;
		if(($totalRows%$Config->max_Page_Size) > 0){
			$totalPages++;
		}
		$sqlfinal = $sql;
		
		$sql_result=mysql_query($sqlfinal) or die($sql.mysql_error());
		$i=0;
		$i = 1;
		$ApiArray = array();
		global $generatePageReport,$headerRepeatOnEvery;$reportPageCount = 0;
		while($line = mysql_fetch_assoc($sql_result))//$MySQLObject->Row())
		{
			$ReceivedProperties = $line;
			$ApiArray[sizeof($ApiArray)] = $ReceivedProperties;
			$j=0;
			foreach($ReceivedProperties as $key => $newvalue)
			{
				$Array[0][$j] = $key;
				$Array[$i][$j] = trim($newvalue);
				++$j;
			}
			$i++;
			if(@$headerRepeatOnEvery > 0){
				if($i%@$headerRepeatOnEvery == 0){
					if($isHorizontal){
						$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS,$isHorizontal);
					}else{
						$returnHtml .=generateReportTable($Array,$indexRequired,$PARAMS);
					}
					$reportPageCount++;
					$ApiArray = array();
					$i=0;
				}
			}else{ /* Noting to do here*/}
		}
		if($isHorizontal){
			$returnHtml .=generateSortabaleTable($Array,$_columnOrder);
		}else{
			$returnHtml .=generateSortabaleTable($Array,$_columnOrder);
		}
		
		
		$pagingCode = '<button class="btn btn-space btn-primary" style="float:left;margin:5px;" onclick="submitOrder();"><i class="icon icon-left mdi mdi-save"></i> <span id="">Submit File Order</span></button><br><hr>';
		$printCode = '<script>$( "#myList" ).sortable();$( "#myList" ).disableSelection();
		function submitOrder(){
  var order1 = $(\'#myList\').sortable(\'toArray\').toString();
             //alert("Order 1:" + order1 + ""); //Just showing update
             $.ajax({
                 type: "POST",
                 url: "orderSaveFE.php?TNAME='.$TNAME.'&COLUMN_NAME='.$COLUMN_NAME.'&ORDER_COLUMN='.$ORDER_COLUMN.'&tsd_id=1",
                 data: "order1=" + order1,
                 dataType: "json",
                 success: function (data) {
					 $("#htm2display").html("Files Submitted SuccessFully");
					// parent.$.notify("Files Submitted SuccessFully", "success");
					 //parent.$("#mod-success").modal("hide");
				 }
             });/**/

 } </script>';
		
			return $pagingCode."<hr>".$returnHtml.$printCode;
		
}
function generateSortabaleTable($Array,$_columnOrder){
	global $PageName;
	$trClass = "";
	$tdClass = "";
	$html ="";
	$i = 0;
	for($i=0;$i<sizeof($Array);$i++){
	$altRow = "";
		if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
		}
		if($i==1){
			$html .= "<div id=\"list2\" class=\"dd\" id=\"sortable\">\n\t<ol class=\"dd-list\" id='myList'>";
		}
		$HookAdded = False;
		for($j=0;$j<sizeof($Array[$i]);$j++){
			if($i>=1  && $HookAdded == false ){
			$html .= "\n\t\t<li style='width:100%' data-id=\"".$Array[$i][$_columnOrder]."\" id=\"".$Array[$i][$_columnOrder]."\" class=\"dd-item dd3-item ui-state-default\"><div class=\"row\" $altRow style='width:100%' ><div class=\"dd-handle dd3-handle\">PageOrder $_columnOrder</div><i class=\"fa fa-file\" aria-hidden=\"true\"></i><div class='column'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";
			$HookAdded= true;
			}
			if($i==0){ 
			$SQLORDERBY = "";
			if( (isset($_GET['SQLORDERBY']) && isset($_GET['SQLORDERBYTYPE' ]) ) && $_GET['SQLORDERBY'] == $Array[$i][$j] ){
				if($_GET['SQLORDERBYTYPE'] == "desc"){
					$_GET['SQLORDERBYTYPE'] = "asc";
				}else{
					$_GET['SQLORDERBYTYPE'] = "desc";
				}
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=".$_GET['SQLORDERBYTYPE'];
			}else{
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=asc";		
			}
			if((!isset($_GET['print_report']) && !$isHorizontal) || (!isset($_GET['print_page']) && !$isHorizontal)){
				//$html .="<td valign=top $tdClass>"."<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".$Array[$i][$j]."</a></td>";
				$html .="<div valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\" style=\"background-color:".$colorNum.
				"padding:5px;float:left\">";
				$html .= "<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".
				//$Array[$i][$j]
				FE_TitleDisplayFormat($Array[$i][$j])."</a>";
				$html .="</div>";
				}
			}else{
				$html .='<div class="column" style="background-color:'.$colorNum.';padding:5px">'.$Array[$i][$j].'</div>';
			}
		}
		$html.="</div></li>";
				
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
				if($i%2 == 0){$colorNum="#EEEEEE";}
				else{$colorNum="#FFFFFF";}
				
	}
	$html.= "\n\t</ol></div>";
	return $html."";
}
function queryToArray($sql){
		include_once("lib/db.class.php");
		include_once("lib/config.class.php");
		include_once("LoginProcess.php"); 
		$Array =array();

		global $db_name,$PageName;
		$StartFrom = 0;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
		$sqlfinal = $sql;
		$sql_result=mysql_query($sqlfinal) or die($sql.mysql_error());
		$i=0;
		/*while($line = mysql_fetch_assoc($res)){
			$line;
			//$optionArray[$i]['VALUE']  = $line[0];
			//$optionArray[$i]['LABEL']  = $line[1];
			//++$i;
		}/**/
		$i = 1;
		while($line = mysql_fetch_assoc($sql_result))//$MySQLObject->Row())
		{
			$ReceivedProperties = $line;
			$j=0;
			foreach($ReceivedProperties as $key => $newvalue)
			{
				$Array[0][$j] = $key;
				$Array[$i][$j] = trim($newvalue);
				++$j;
			}
			$i++;
		}
		//$returnHtml=generateReportTable($Array,0,$PARAMS);
		
		return $Array;
	
}
function queryToAssocArray($sql){
		include_once("lib/db.class.php");
		include_once("lib/config.class.php");
		include_once("LoginProcess.php"); 
		$Array =array();

		global $db_name,$PageName;
		$StartFrom = 0;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
		$sqlfinal = $sql;
		$sql_result=mysql_query($sqlfinal) or die($sql.mysql_error());
		$i=0;
		/*while($line = mysql_fetch_assoc($res)){
			$line;
			//$optionArray[$i]['VALUE']  = $line[0];
			//$optionArray[$i]['LABEL']  = $line[1];
			//++$i;
		}/**/
		$i = 1;
		while($line = mysql_fetch_assoc($sql_result))//$MySQLObject->Row())
		{
			$ReceivedProperties = $line;
			$j=0;
			foreach($ReceivedProperties as $key => $newvalue)
			{
				$Array[0][$j] = $key;
				$Array[$i][$key] = trim($newvalue);
				++$j;
			}
			$i++;
		}
		//$returnHtml=generateReportTable($Array,0,$PARAMS);
		
		return $Array;
	
}
function queryToChartData($sql,$column,$isCatData=false){
		include_once("lib/db.class.php");
		include_once("lib/config.class.php");
		include_once("LoginProcess.php");
		$Array =array();

		global $db_name,$PageName;
		$StartFrom = 0;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
		$sqlfinal = $sql;
		$sql_result=mysql_query($sqlfinal) or die($sql.mysql_error());
		$i=0;
		$i = 1;
		while($line = mysql_fetch_assoc($sql_result))//$MySQLObject->Row())
		{
			$ReceivedProperties = $line;
			$j=0;
			for($j=0;$j<sizeof($column);++$j){
				if($isCatData){
					$Array[]=$ReceivedProperties[$column[$j]];
				}else{
					$Array[$i][$column[$j]] = $ReceivedProperties[$column[$j]];
					if($j==0){
						unset($Array[$i][$column[$j]]);
						$Array[$i]['name'] = $ReceivedProperties[$column[$j]];	
					}
					if($j==1){
						unset($Array[$i][$column[$j]]);
						$Array[$i]['y'] = $ReceivedProperties[$column[$j]]+0;
					}
					if($i%2 == 0)
					$Array[$i]['sliced']="true";
				}
			}
			$i++;
		}
		foreach($Array as $key => $value){
			$Array1[] = $value;
		}
		//print_r($Array1);
		return $Array1;
	
}
function pagingCode($totalPages,$PARAMS){
		global $PageName;
		$SQLORDERBY = "";
			if( (isset($_GET['SQLORDERBY']) && isset($_GET['SQLORDERBYTYPE' ]) && $_GET['SQLORDERBY'] != "" )){
				if($_GET['SQLORDERBYTYPE'] == "desc"){
					$_GET['SQLORDERBYTYPE'] = "asc";
				}else{
					$_GET['SQLORDERBYTYPE'] = "desc";
				}
				$SQLORDERBY = "&SQLORDERBY=".@$_GET['SQLORDERBY']."&SQLORDERBYTYPE=".@$_GET['SQLORDERBYTYPE'];
			}else{
				$SQLORDERBY = "&SQLORDERBY=".@$_GET['SQLORDERBY']."&SQLORDERBYTYPE=asc";		
			}
		$TillPageNumber = ((getValueGPC('pageNumber')+5)> $totalPages) ? $totalPages :(getValueGPC('pageNumber')+5);
		$FromPageNumber = ((getValueGPC('pageNumber')-5)> 0) ?  (getValueGPC('pageNumber')-5) :1;
		$pagingCode ="";
		if(getValueGPC('pageNumber')>1)
		$pagingCode.=" <a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')-1)." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')-1)."');return false;\" class='btn btn-sm btn-info'>&lt;PREV</a> | ";
		for($i=$FromPageNumber;$i<=$TillPageNumber;++$i){
		$pagingCode.="<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=$i onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=$i');return false;
		\" class='btn  btn-sm btn-info'>";
			if( getValueGPC('pageNumber') == $i ) {
			  $pagingCode.="<b><u>$i</b></u>";
			}else{ $pagingCode.="$i"; }
		$pagingCode.="</a> | ";
		}
		if((getValueGPC('pageNumber')+1)<=$totalPages)
		$pagingCode.="<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')+1)." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')+1)."');return false;\" class='btn btn-sm btn-info'>NEXT&gt;</a> ";
		$pagingCode .= "| <a href=\"$PageName?checksum=".getValueGPC('checksum')."&print_report=true&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\" class='btn btn-sm btn-success'>Print Report</a>  ";
		$pagingCode .= "| <a href=\"$PageName?checksum=".getValueGPC('checksum')."&print_page=true&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."\" target=\"_ReportPrint\" class='btn  btn-sm btn-success'>Print Page</a>";
		$pagingCode .= "| <a href=\"$PageName?AJAX=Y&checksum=".getValueGPC('checksum')."&print_report=true&excel_export=true&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\" class='btn btn-sm btn-warning'>Download Report</a>  ";
		$pagingCode .= "| <a href=\"$PageName?AJAX=Y&checksum=".getValueGPC('checksum')."&print_report=true&excel_export=csv&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\" class='btn btn-sm btn-success'>Export CSV Report</a>  ";
		$pagingCode .= "| <a href=\"$PageName?AJAX=Y&checksum=".getValueGPC('checksum')."&print_report=true&excel_export=xml&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\" class='btn btn-sm btn-success'>Export XML Report</a>  ";
		$pagingCode .= "| <a href=\"$PageName?AJAX=Y&checksum=".getValueGPC('checksum')."&print_report=true&excel_export=json&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\" class='btn btn-sm btn-success'>Export Json Report</a>  ";
		return $pagingCode;
}
function generateReportTable($Array,$indexRequired = 0,$PARAMS,$isHorizontal=false){
	global $PageName;
	$trClass = "";
	$tdClass = "";
	$html ="";
	if($isHorizontal){
	$html =	"<style type='text/css'>
		
		.hori {float: left;}
		.horitd {display: block;padding: 5px; border: 1px dotted blue;}
	</style>";
		$trClass =' class="hori" ';
		$tdClass =' class="horitd" ';
	}
	if(isset($_REQUEST['print_report']) || isset($_REQUEST['print_page'])){
		$html .= "<table width=100% border=1px cellspacing=0px class='table'>";
	}else{
		$html .= "<table width=100% border=0px cellspacing=0px class='table'>";
	}
	
	
	$colorNum='#eeeeee';
	$counter = 0;
	for($i=0;$i<sizeof($Array);$i++){
	$altRow = "";
		if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
		}
		$html .="\n\t<tr bgcolor=$colorNum $altRow $trClass><td>&nbsp;</td>";
		if($indexRequired == 1 && $i >= 1){
			$html .="\n\t\t<td valign=top $tdClass>".($i)."</td>";
		}else if($indexRequired == 1){
			$html .="\n\t\t<td valign=top $tdClass>S.No.</td>";
		}
		for($j=0;$j<sizeof($Array[$i]);$j++){
			if($i==0){ 
			$SQLORDERBY = "";
			if( (isset($_GET['SQLORDERBY']) && isset($_GET['SQLORDERBYTYPE' ]) ) && $_GET['SQLORDERBY'] == $Array[$i][$j] ){
				if($_GET['SQLORDERBYTYPE'] == "desc"){
					$_GET['SQLORDERBYTYPE'] = "asc";
				}else{
					$_GET['SQLORDERBYTYPE'] = "desc";
				}
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=".$_GET['SQLORDERBYTYPE'];
			}else{
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=asc";		
			}
				if((!isset($_GET['print_report']) && !$isHorizontal) || (!isset($_GET['print_page']) && !$isHorizontal)){
					//$html .="<td valign=top $tdClass>"."<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".$Array[$i][$j]."</a></td>";
					$html .="\n\t\t<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">";
					$html .= "<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".
				//$Array[$i][$j]
					FE_TitleDisplayFormat($Array[$i][$j])."</a>";
					$html .="</th>";
				}
			}else{
				$html .="\n\t\t<td valign=top $tdClass>".$Array[$i][$j]."</td>";
			}
		}
		$html.="\n\t</tr>";
				
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
				if($i%2 == 0){$colorNum="#EEEEEE";}
				else{$colorNum="#FFFFFF";}
				
	}
	return $html."</table>";
}
function generateReportTableEcho($Array,$indexRequired = 0,$PARAMS,$isHorizontal=false){
	global $PageName;
	$trClass = "";
	$tdClass = "";
	$html ="";
	if($isHorizontal){
	echo	"<style type='text/css'>
		
		.hori {float: left;}
		.horitd {display: block;padding: 5px; border: 1px dotted blue;}
	</style>";
		$trClass =' class="hori" ';
		$tdClass =' class="horitd" ';
	}
	if(isset($_REQUEST['print_report']) || isset($_REQUEST['print_page'])){
		echo "<table width=100% border=1px cellspacing=0px class='table'>";
	}else{
		echo "<table width=100% border=0px cellspacing=0px class='table'>";
	}
	
	
	$colorNum='#eeeeee';
	$counter = 0;
	for($i=0;$i<sizeof($Array);$i++){
	$altRow = "";
		if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
		}
		echo "\n\t<tr bgcolor=$colorNum $altRow $trClass><td>&nbsp;</td>";
		if($indexRequired == 1 && $i >= 1){
			echo "\n\t\t<td valign=top $tdClass>".($i)."</td>";
		}else if($indexRequired == 1){
			echo "\n\t\t<td valign=top $tdClass>S.No.</td>";
		}
		for($j=0;$j<sizeof($Array[$i]);$j++){
			if($i==0){ 
			$SQLORDERBY = "";
			if( (isset($_GET['SQLORDERBY']) && isset($_GET['SQLORDERBYTYPE' ]) ) && $_GET['SQLORDERBY'] == $Array[$i][$j] ){
				if($_GET['SQLORDERBYTYPE'] == "desc"){
					$_GET['SQLORDERBYTYPE'] = "asc";
				}else{
					$_GET['SQLORDERBYTYPE'] = "desc";
				}
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=".$_GET['SQLORDERBYTYPE'];
			}else{
				$SQLORDERBY = "&SQLORDERBY=".$Array[$i][$j]."&SQLORDERBYTYPE=asc";		
			}
				if((!isset($_GET['print_report']) && !$isHorizontal) || (!isset($_GET['print_page']) && !$isHorizontal)){
					//$html .="<td valign=top $tdClass>"."<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".$Array[$i][$j]."</a></td>";
					echo "\n\t\t<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">";
					echo  "<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".
				//$Array[$i][$j]
					FE_TitleDisplayFormat($Array[$i][$j])."</a>";
					echo "</th>";
				}
			}else{
				echo "\n\t\t<td valign=top $tdClass>".$Array[$i][$j]."</td>";
			}
		}
		echo "\n\t</tr>";
				
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
				if($i%2 == 0){$colorNum="#EEEEEE";}
				else{$colorNum="#FFFFFF";}
				
	}
	 echo  "</table>";
}
function chartsInIt(){
	$Config = new get_config();
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRoller.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerSeriesData.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerLineChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerSplineChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerAreaChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerAreaSplineChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerBarChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerColumnChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerPieChart.php');
  require_once($Config->ApplicationBasePath.'chartWrap/test/_assets/HighRoller/HighRollerScatterChart.php');
}
function addChartsInIt($chartName,$ChartObject,$DataSeries,$Text="",$_xAxis="",$_yAxis=""){  

  foreach($DataSeries as $key => $value){
		$DataSeries1[] = $value;
	}
		$ChartObject->chart->renderTo = $chartName;
		$ChartObject->title->text = $Text;
		$ChartObject->addSeries($DataSeries);
		$ChartObject->yAxis->title->text = $_yAxis;
		$ChartObject->xAxis->title->text = $_xAxis;

		//$ChartObject->enableAutoStep();
 //   echo "<script type=\"text/javascript\">".$ChartObject->renderChart()."<script>";
//	echo "<ppre>		"." var $chartName =  ".$ChartObject->getChartOptionsObject()."</ppre>";
echo "
	<div id=\"$chartName\" style=\"display:block; float: left; width:90%; margin-bottom: 20px;\"></div>
    <div class=\"clear\"></div>
	<script type=\"text/javascript\">
	"." var $chartName =  ".$ChartObject->getChartOptionsObject()."
	".$ChartObject->renderChart()."\n
	 $(\"pre.htmlCode\").snippet(\"html\",{style: \"the\", showNum: false});
        $(\"pre.phpCode\").snippet(\"php\",{style: \"the\", showNum: false});
	 </script>";
	
	
}
function generateDataSeries($ChartData,$ElementName = 'Items'){
		$series1 = new HighRollerSeriesData();
		$series1->title="sales Chart Items";
		$series1->addName($ElementName)->addData($ChartData);
		$series1->addName($ElementName)->formatter="function() {return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %'}";	//$series1->addName($ElementName)->xAxis=$_xAxis;
		return $series1;
		
}
function array_to_xml(array $arr,SimpleXMLElement $xml){
	foreach ($arr as $k => $v) {
        is_array($v)
            ? array_to_xml($v, $xml->addChild($k))
            : $xml->addChild($k, $v);
    }
    return $xml;
}

?>