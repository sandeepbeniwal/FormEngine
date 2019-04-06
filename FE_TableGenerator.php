<?php

function generateAssocTable($Array,$TableHeading='',$ColumnHeading = true){
	//print_r($Array[1]);
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['TABLE_HEADING']=$TableHeading;
				$Array['CONFIG']['COLUMN_HEADING']=$ColumnHeading;
				return json_encode($Array);
		}
	$returnTableHtml = '';
	$returnTableHtml .= "<table class='table'>";
	if($ColumnHeading){
		$returnTableHtml .= "<tr>";
		foreach($Array[1] as $newkey => $newvalue){
			if(!is_numeric($newkey))
				$returnTableHtml .= "<th>$newkey</th>";
		}
		$returnTableHtml .= "</tr>";
		
	}
	for($i=1;$i<=sizeOf($Array);++$i){
		$returnTableHtml .= "<tr>";
		foreach($Array[$i] as $newkey => $newvalue){
			if(!is_numeric($newkey))
				$returnTableHtml .= "<td>$newvalue</td>";
		}
		$returnTableHtml .= "</tr>";
	}
	$returnTableHtml .= "</table>";
	return $returnTableHtml;
}
function generateBlockTable($Array,$TableHeading='',$ColumnHeading = true){
	//print_r($Array);
	$returnTableHtml = '';
	$returnTableHtml .= "<div>";
	if($ColumnHeading){
		foreach($Array[1] as $newkey => $newvalue){
			if(is_numeric($newkey))
			$returnTableHtml .= "<span> $newkey </span>";
		}		
	}
	for($i=1;$i<=sizeOf($Array);++$i){
		$returnTableHtml .= "<div>";
		foreach($Array[$i] as $newkey => $newvalue){
			if(is_numeric($newkey))
			$returnTableHtml .= " $newvalue ";
		}
		$returnTableHtml .= "</div>";
	}
	$returnTableHtml .= "<div>";
	return $returnTableHtml;
}
function generateDynaTable($Array,$indexRequired = 0){
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				return json_encode($Array);
		}
		$html= "<table width=100% border=0px cellspacing=0px class='table'>";
		$colorNum='#eeeeee';
		$altRow = "";
		$counter = 0;
		for($i=0;$i<sizeof($Array);$i++){
			$altRow = "";
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			$html .="<tr bgcolor=$colorNum $altRow><td>&nbsp;</td>";
			if($indexRequired == 1 && $i >= 1){
				$html .="<td valign=top>".($i)."</td>";
			}else if($indexRequired == 1){
				$html .="<td valign=top>S.No.</td>";
			}
			$j=0;
			for($j=0;$j<sizeof($Array[1]['ASSOC']);$j++){
				if(isset($Array[$i][$j])){
					if($i==0)
						$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">".FE_TitleDisplayFormat($Array[$i][$j])."</th>";
					else
						$html .="<td valign=top>".$Array[$i][$j]."</td>";
				}
			}
			$html.="</tr>";
		
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
	
		}
		return $html.="</table>";
	}
	function generateDynaTableChooseLink($Array,$indexRequired = 0,$Operation = 'choose',$MainColumn='',$PageName='',$PARAMS = ''){
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['OPERATION']=$Operation;
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				$Array['CONFIG']['MAINCOLUMN']=$MainColumn;
				$Array['CONFIG']['PAGENAME']=$PageName;
				$Array['CONFIG']['ADD_DELETE_ROW']=$addDeleteRow;
				$Array['CONFIG']['PARAMS']=$PARAMS;
				return json_encode($Array);
		}
	$html= "<table width=100% border=0px cellspacing=0px class='table'>";
		$colorNum='#eeeeee';
		$counter = 0;
		$altRow  ="";
		//echo "<pre>";print_r($Array);echo "</pre>";
		for($i=0;$i<sizeof($Array);$i++){
			$altRow = "";
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			$htmlOnclick = "";
			$html .="<tr bgcolor=$colorNum $altRow onMouseOver=\"this.bgColor='yellow';\" onMouseOut=\"this.bgColor='$colorNum';\""."><td>&nbsp;</td>";
			if($PageName != '')
				$htmlOnclick ="onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."$PARAMS'+'&$Operation=".$Array[$i][$MainColumn]."');return false;\"";
				$html .="> 		<td>&nbsp;</td>";
			if($indexRequired == 1 && $i >= 1){
				$html .="<td valign=top>".($i)."</td>";
			}else if($indexRequired == 1){
				$html .="<td valign=top>S.No.</td>";
			}
			$j=0;
			for($j=0;$j<sizeof($Array[1]['ASSOC']);$j++){
				if(isset($Array[$i][$j]) && ! is_array($Array[$i][$j])){
					if($i==0)
					$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">".FE_TitleDisplayFormat($Array[$i][$j])."</th>";
				else
					$html .="<td valign=top $htmlOnclick>".$Array[$i][$j]."</td>";
					//$html .="<td valign=top $htmlOnclick >".$Array[$i][$j]."</td>";
				}else if(is_array(@$Array[$i][$j])){
					if(isset($Array[$i][$j]) && ! is_array($Array[$i][$j])){
					if($i==0)
					$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j]['DATA'])."\">".FE_TitleDisplayFormat($Array[$i][$j]['DATA'])."</th>";
				else
					$html .="<td valign=top $htmlOnclick>".$Array[$i][$j]['DATA']."</td>";
					$html .="<td valign=top>".$Array[$i][$j]['DATA']."</td>";
				}
			}
			$html.="</tr>";
		
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
	
		}
	}
		return $html.="</table>";
	}
	function generateTable($Array,$indexRequired = 0){
	//echo print_r($Array);
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['OPERATION']=$Operation;
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				return json_encode($Array);
		}
	$html= "<table width=100% border=0px cellspacing=0px class='table'>";
	$colorNum='#eeeeee';
	$counter = 0;
	for($i=0;$i<sizeof($Array);$i++){
	$altRow = "";
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
		$html .="<tr bgcolor=$colorNum $altRow ><td>&nbsp;</td>";
		if($indexRequired == 1 && $i >= 1){
			$html .="<td valign=top>".($i)."</td>";
		}else if($indexRequired == 1){
			$html .="<td valign=top>S.No.</td>";
		}
		for($j=0;$j<sizeof($Array[$i]);$j++){
				if($i==0)
					$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">".FE_TitleDisplayFormat($Array[$i][$j])."</th>";
				else
					$html .="<td valign=top>".$Array[$i][$j]."</td>";
		}
		$html.="</tr>";
		
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
	
	}
	//echo $html;
	return $html.="</table>";
	}
	function generateTableChooseLink($Array,$indexRequired = 0,$Operation = 'choose',$MainColumn='',$PageName='',$PARAMS = ''){
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['OPERATION']=$Operation;
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				$Array['CONFIG']['MAINCOLUMN']=$MainColumn;
				$Array['CONFIG']['PAGENAME']=$PageName;
				$Array['CONFIG']['ADD_DELETE_ROW']=$addDeleteRow;
				$Array['CONFIG']['PARAMS']=$PARAMS;
				return json_encode($Array);
		}
		$html= "<table width=100% border=0px cellspacing=0px class='table'>";
		$colorNum='#eeeeee';
		$counter = 0;
		for($i=0;$i<sizeof($Array);$i++){
		$altRow = "";
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			$html .="<tr bgcolor=$colorNum $altRow onMouseOver=\"this.bgColor='grey';\" onMouseOut=\"this.bgColor='$colorNum';\"  
			onclick=\"javascript:showData('$PageName','".getValueGPC(checksum)."$PARAMS'+'&$Operation=".$Array[$i][$MainColumn]."');return false;\"> 		<td>&nbsp;</td>";
			if($indexRequired == 1 && $i >= 1){
				$html .="<td valign=top>".($i)."</td>";
			}else if($indexRequired == 1){
				$html .="<td valign=top>S.No.</td>";
			}
			for($j=0;$j<sizeof($Array[$i]);$j++){
				if($i==0)
					$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">".FE_TitleDisplayFormat($Array[$i][$j])."</th>";
				else
					$html .="<td valign=top>".$Array[$i][$j]."</td>";
			}
			$html.="</tr>";
		
				if($i%4 == 0){$colorNum="#FFDDFF";}
				else if($i%4 == 1){$colorNum="lightblue";}
				else if($i%4 == 2){$colorNum="pink";}
				else if($i%4 == 3){$colorNum="white";}
				else {$colorNum="lightgreen";}
	
		}
	//echo $html;
	return $html.="</table>";
	}
	function generateTableEditLink($Array,$indexRequired = 0,$Operation = 'Edit',$MainColumn='',$PageName='',$addDeleteRow = false){
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['OPERATION']=$Operation;
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				$Array['CONFIG']['MAINCOLUMN']=$MainColumn;
				$Array['CONFIG']['PAGENAME']=$PageName;
				$Array['CONFIG']['ADD_DELETE_ROW']=$addDeleteRow;
				return json_encode($Array);
		}
		//echo sizeof($Array);
		//print_r($Array);
		$html= "<table class=\"git_table table\" >";
//		$html .="<tbody>";
		$colorNum='#eeeeee';
		$counter = 0;
		if(isset($_REQUEST['Search'])){
			$PARAMS .="&Search=".$_REQUEST['Search'];
		}
		for($i=0;$i<sizeof($Array);$i++){
			$altRow = "";
			$ValueToEdit = $Array[$i][$MainColumn];
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			if($i == 1){
				$html .="";
			}
			$html .="<tr bgcolor=$colorNum $altRow onMouseOver=\"this.bgColor='grey';\" onMouseOut=\"this.bgColor='$colorNum';\"";
			//if($i>0){ 
				
				$html1 =<<<HTML_OUTPUT
		onclick="javascript:showData('$PageName','$_GET[checksum]'+'&$Operation=$ValueToEdit');return false; "
HTML_OUTPUT;
				//$html .=$html1;
			//}//  
			$html .=">";// <td>&nbsp;</td>";
			if($addDeleteRow == true){
				$html2 =<<<HTML_OUTPUT
		onclick="javascript:if(confirm('Really want to remove')){showData('$PageName','$_GET[checksum]'+'&RemoveSelected=$ValueToEdit');}return false; "
HTML_OUTPUT;
				if($i==0){
					$html .="<th valign=top >Operation</th>";
				}else{
					$html .="<td valign=top $html2>Remove</td>";
				}
			}
			if($indexRequired == 1 && $i >= 1){
				$html .="<td valign=top $html1>".($i)."</td>";
			}else if($indexRequired == 1){
				$html .="<th valign=top >S.No.</th>";
			}
			for($j=0;$j<sizeof($Array[$i]);$j++){
				if($i==0){
					$SQLORDERBY = "";
					if( (isset($_REQUEST['SQLORDERBY']) && isset($_REQUEST['SQLORDERBYTYPE' ]) ) && $_REQUEST['SQLORDERBY'] == $Array[$i][$j] ){
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
							$html .="\n\t\t<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">";
							$html .= "<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."');return false;\">".FE_TitleDisplayFormat($Array[$i][$j])."</a>";
							$html .="</th>";
						}else{
					
							$html .="<th valign=top class=\"".FE_TitleDisplayFormat($Array[$i][$j])."\">".FE_TitleDisplayFormat($Array[$i][$j])."</th>";
						}
				}else
					$html .="<td valign=top $html1>".$Array[$i][$j]."</td>";
			}
			
			$html.="</tr>";
		
				if($i%4 == 0){$colorNum="EEEEEE";}
				else if($i%4 == 1){$colorNum="FFFFFF";}
				else if($i%4 == 2){$colorNum="EEEEEE";}
				else if($i%4 == 3){$colorNum="FFFFFF";}
				else {$colorNum="lightgreen";}
	
		}
//		$html .="</tbody>";
		return $html.="</table>"."<script>//alert(\"\");
	
		//updateTables();alert('done');
		</script>";
	}
	
		function generateTableEditLinkMessage($Array,$indexRequired = 0,$Operation = 'Edit',$MainColumn='',$PageName=''){
		//print_r($Array);
		$html= "";
		$colorNum='#eeeeee';
		$counter = 0;
		for($i=1;$i<sizeof($Array);$i++){
			$altRow = "";
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			if($i == 1){
				$html .="";
			}
			$html1 =<<<HTML_OUTPUT
			<div style="background-color:$colorNum" $altRow onMouseOver="this.bgColor='grey';"
			onMouseOut="this.bgColor='$colorNum';"
HTML_OUTPUT;
			$html .=$html1;

			if($i>0){ 
				$ValueToEdit = $Array[$i][$MainColumn];
				$html1 =<<<HTML_OUTPUT
				onclick="javascript:showData('$PageName','$_GET[checksum]'+'&$Operation=$ValueToEdit');return false; "
HTML_OUTPUT;

				$html .=$html1;
			}
			$html .= " > ";

			//if($indexRequired == 1 && $i >= 1){
			//	$html .="<span valign=top>".($i)."</span>";
			//}else if($indexRequired == 1){
			//	$html .="<span valign=top>S.No.</span>";
			//}
			$html .="<span valign=top style=\"overflow:auto\">";
			for($j=1;$j<sizeof($Array[$i]);$j++){
				$Array[$i][$j] = str_ireplace("\n", "<br/>", $Array[$i][$j]);
				$Array[$i][$j] = str_ireplace("\r", "<br/>", $Array[$i][$j]);
				$html .="<br/>";
				if($j == 1){$html .="From:";}
				else if($j == 2){$html .="Email:";}
				else if($j == 3){$html .="Message:";}
				else if($j == 5){$html .="My Reply:<br>";}
				if($j == 4 && $Array[$i][$j] == "Y"){}
				else if($j == 4 && $Array[$i][$j] != "Y"){$html .="<b>Please Reply To this message</b>";}
				else{	$html .= $Array[$i][$j]; }
//				$html .="</pre>";
				//if($j == 5 || ){$html .="</pre>";}
			}
			$html .="</span>";
			$html.="</div>";
		
				if($i%2 == 0){$colorNum="#eeeeee";}
				else if($i%2 == 1){$colorNum="white";}
					
		}
		return $html;
	}
	function generateTableEditLinkNoAjaxNewWindow($Array,$indexRequired = 0,$Operation = 'Edit',$MainColumn='',$PageName=''){
		//echo sizeof($Array);
		//print_r($Array);
		global $isApiCall;
		if($isApiCall){
				$Array['CONFIG']['OPERATION']=$Operation;
				$Array['CONFIG']['INDEX_REQUIRED']=$indexRequired;
				$Array['CONFIG']['MAINCOLUMN']=$MainColumn;
				$Array['CONFIG']['PAGENAME']=$PageName;
				$Array['CONFIG']['ADD_DELETE_ROW']=$addDeleteRow;
				$Array['CONFIG']['OPEN_NEW']='true';
				return json_encode($Array);
		}
		$html= "<table class=\"responsive table\" >";
//		$html .="<tbody>";
		$colorNum='#eeeeee';
		$counter = 0;
		for($i=0;$i<sizeof($Array);$i++){
			$altRow = "";
			$ValueToEdit = $Array[$i][$MainColumn];
			if($i%2 == 1){
				$altRow = 	"id = \"alt-row\"";
			}
			if($i == 1){
				$html .="";
			}
			$html .="<tr bgcolor=$colorNum $altRow onMouseOver=\"this.bgColor='yellow';\" onMouseOut=\"this.bgColor='$colorNum';\"";
			if($i>0){ 
				
				$html1 =<<<HTML_OUTPUT
		onclick="javascript:window.open('$PageName?checksum=$_GET[checksum]'+'&$Operation=$ValueToEdit');return false; "
HTML_OUTPUT;
				$html .=$html1;
			}  
			$html .="> <td>&nbsp;</td>";
			if($indexRequired == 1 && $i >= 1){
				$html .="<th valign=top>".($i)."</th>";
			}else if($indexRequired == 1){
				$html .="<th valign=top>S.No.</th>";
			}
			for($j=0;$j<sizeof($Array[$i]);$j++){
				if($i==0)
					$html .="<th valign=top>".$Array[$i][$j]."</th>";
				else
					$html .="<td valign=top>".$Array[$i][$j]."</td>";
			}
			$html.="</tr>";
		
				if($i%4 == 0){$colorNum="EEEEEE";}
				else if($i%4 == 1){$colorNum="FFFFFF";}
				else if($i%4 == 2){$colorNum="EEEEEE";}
				else if($i%4 == 3){$colorNum="FFFFFF";}
				else {$colorNum="lightgreen";}
	
		}
//		$html .="</tbody>";
		return $html.="</table>"."<script>//alert(\"\");
	
		//updateTables();alert('done');
		</script>";
	}
		
	function FE_TitleDisplayFormat($VALUE){
	return ucwords(strtolower(str_ireplace(" ID","",str_ireplace("PR ","",str_ireplace("_"," ",$VALUE)))));
	}
?>