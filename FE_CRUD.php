<?php
includeOnce('MysqlUtilities.class.php');
includeOnce('Validate.class.php');
 
class gitFeCrudOps extends Validate {

	/* validate method for provided objects */
	public function validateDBCompatibility($Column='',$object){
		$ERROR = false;
		if($object->getKeyValue($Column) == NULL and $object->getKeyValue($Column,"_IS_NULLABLE") == "NO"){ 
			$ERROR = true;
		}else if($object->getKeyValue($Column,"_DATA_TYPE") == "varchar" && $object->getKeyValue($Column,"_DATA_TYPE") == "char" && $object->getKeyValue($Column,"_DATA_TYPE") == "text"){
			if($object->getKeyValue($Column,"_CHARACTER_MAXIMUM_LENGTH") > strlen($object->getKeyValue($Column))){
				$ERROR = true;
			}
			if($object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") !=null && $object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") >0){
				if($object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") < strlen($object->getKeyValue($Column))){
					$ERROR = true;
				}
			}
		}else if($object->getKeyValue($Column,"_DATA_TYPE") == "int" && $object->getKeyValue($Column,"_DATA_TYPE") == "bigint"){
			if (!(ereg("^([0-9])+$", $object->getKeyValue($Column)))){
				$ERROR = true;
			}
			if($object->getKeyValue($Column,"_NUMERIC_PRECISION") > strlen($object->getKeyValue($Column))){
				$ERROR = true;
			}
			if($object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") !=null && $object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") >0){
				if($object->getKeyValue($Column,"_CHARACTER_MINIMUM_LENGTH") < strlen($object->getKeyValue($Column))){
					$ERROR = true;
				}
			}
		}
		return $ERROR;
	}
	
	/* search method for provided objects */
	public function searchOperation($SelectColumns = '*' , $SearchTerm='',$object,$pagingData=true,$data4json = false){
		global $user_details;
		$Config = new get_config();
		$SearchInView =false;
		if($SelectColumns == '' || $SelectColumns == '*'){
		 $SelectColumns = $object->ColumnArray;}
		$sql_statement = 'SELECT '.$SelectColumns.' FROM '.$object->TableName.'_view';
		$MySQLObject = new MysqlUtilities($object->DatabaseName,$object->HtmlDebug,$object->Debug);
		$sql_result = $MySQLObject->Query($sql_statement);// or die(mysql_error());//$MySQLObject->ShowError($sql_statement);
		if($line = @mysql_fetch_array($sql_result)){
		 $object->TableName = $object->TableName."_view";
		 $SearchInView =true;
		}
		$sql_statement = 'SELECT '.$SelectColumns.' FROM '.$object->TableName.' ';
		$sql_statementPG = 'SELECT count('.$object->MAINCOLUMN.') as CNT FROM '.$object->TableName.' ';
		$sql_statementSearch = 'SELECT '.$SelectColumns.' FROM '.$object->TableName.' ';
		$sql_statement = '';
		$ColumnList = explode(',', $object->SearchArray);
		if($SearchTerm!=''){
			$sql_statement .=' WHERE ';
			
	 		$addOR = false;
			for($i=0;$i< sizeof($ColumnList);++$i){
				if(parent::isNum($SearchTerm)){
					if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "bigint" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "int" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "float"){
					if($addOR){$sql_statement .=" || ";}
			 		$sql_statement .=" ".$ColumnList[$i] ." = $SearchTerm ";
			 		$addOR = true;
			 		}elseif($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "varchar" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "char" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "text"){
						if($addOR){$sql_statement .=" || ";}
						$sql_statement .=" ".$ColumnList[$i] ." like \"%$SearchTerm%\"";
						$addOR = true;
					}else if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "timestamp" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "date"){
					}else if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "datetime" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "date"){
					}
				}else{
					 if($SearchInView){
						if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "bigint" ){
							if($addOR){$sql_statement .=" || ";}
								$sql_statement .=" ".$ColumnList[$i] ." like \"%$SearchTerm%\"";
								$addOR = true;
							}
					 }						 
					if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "varchar" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "char" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "text"){
						if($addOR){$sql_statement .=" || ";}
						$sql_statement .=" ".$ColumnList[$i] ." like \"%$SearchTerm%\"";
						$addOR = true;
					}else if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "timestamp" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "date"){
					}else if($this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "datetime" || $this->getKeyValue($ColumnList[$i],"_DATA_TYPE",$object) == "date"){
					}//else
				} 	
			}
		}
		$StartFrom = 0;
		$MySQLObject = new MysqlUtilities($object->DatabaseName,$object->HtmlDebug,$object->Debug);
		$sql_statementPG .= $sql_statement;
		$sql_result = mysql_query($sql_statementPG) or $MySQLObject->ShowError($sql_statementPG);
		
		$totalRows = mysql_fetch_array($sql_result);
		$totalRows = $totalRows['CNT'];
		$totalPages= $totalRows/$Config->max_Page_Size;
		if(($totalRows%$Config->max_Page_Size) > 0){
			$totalPages++;
		}
		$StartFrom = 0;
		if(getValueGPC('pageNumber') != "")
			$StartFrom = ((getValueGPC('pageNumber')*$Config->max_Page_Size)-$Config->max_Page_Size);
		else{
			$_GET['pageNumber'] = 1;
		}
		
		if(isset($_REQUEST['SQLORDERBY'])){
					$sql_statement .= " order by ".$_REQUEST['SQLORDERBY']." ";
					if(isset($_REQUEST['SQLORDERBYTYPE'])){
						$sql_statement .= $_REQUEST["SQLORDERBYTYPE"]." ";
					}else{/*Do Nothing*/}
		
		}else{
				$sql_statement .= " order by $ColumnList[0] desc ";
		}
		if(getValueGPC('print_report')=="true"){
		
		}else{
				$sql_statement .= " limit $StartFrom, $Config->max_Page_Size ";
		}
		
		
			//echo $sql_statement;
		//$sql_result = $MySQLObject->Query($sql_statement) or $MySQLObject->ShowError($sql_statement);
		$MySQLObject = new MysqlUtilities($object->DatabaseName,$object->HtmlDebug,$object->Debug);
		$sql_statementSearch.=$sql_statement;
		$sql_result = $MySQLObject->Query($sql_statementSearch) or $MySQLObject->ShowError($sql_statementSearch);
		
		 $totalRows = $MySQLObject->last_found_count;//mysql_num_rows($sql_result);
		//$totalPages= $totalRows/$Config->max_Page_Size;
		//if(($totalRows%$Config->max_Page_Size) > 0){
		//	$totalPages++;
		//}
		//echo $totalPages;
		
		if($totalPages >= 2){
			if(!$data4json){
				if(getValueGPC('print_report')=="true" || getValueGPC('print_page')=="true"){
					if(getValueGPC('print_report_screen')==''){
						echo $printCode="<script>window.print();</script>";
					}
					$pagingCode="";
				}else{
					if(getValueGPC('Search')!=""){
						echo	$pagingCode = $this->pagingCode($totalPages,"Search=$SearchTerm");
					}else{
						echo	$pagingCode = $this->pagingCode($totalPages,"");
					}
				}
			}
			
		}
		//}
		$i = 1;
		$Array =  array();
		$ArrayJson =  array();
		$SelfProperties=$this->getObjectAttributes($object);
		while($line = mysql_fetch_array($sql_result)) { //$MySQLObject->Row())
			$ReceivedProperties = $line;
//			print_r($ReceivedProperties);
			$j=0;
			foreach($SelfProperties as $key => $value) { //Each variable is compared to variables of the object passed
				foreach($ReceivedProperties as $newkey => $newvalue){
					if(strtoupper($key) == strtoupper($newkey)) { //if a match is found, this object's vars are populated;
						$Array[0][$j] = $key;
						$ArrayJson[0][$key]=$key;
						$Array[$i][$j] = trim($newvalue);
						$ArrayJson[$i][$key]=trim($newvalue);
								//echo $key." - ".$newvalue."<br>";
								++$j;
								//break;
					}					
				}	
			}
			$i++;
		}
		if($i < 1){
			echo "No Result Found";
			$Array['FE_TYPE'] = 'MESSAGE';
			$Array['Result'] = "No Result Data Available";
			if($isApiCall){
			}else{
				echo $Array[1]['Result'];
			}
		}
		if($data4json){
			$Array = array();
			$_a = sizeof($Array);
			$Array[$_a]['FE_PAGE_COUNT'] = floor($totalPages);
			$Array[$_a]['FE_CURRENT_PAGE'] = getValueGPC('pageNumber');
			$_a = $Array;
			$Array = array();
			$Array['FE_TYPE'] = 'TABLE-DATA';
			$Array['DATA'] = $ArrayJson;
			$Array['CONFIG'] = $_a;
		}
		return $Array;
		
	}
	/* Input method for provided objects */
	public function insertIntoTable($table='',$object){
		$mainIdColumn =  "$"."thisOutput = $"."object->get".$object->MAINCOLUMN."();";
		eval($mainIdColumn);
		if($thisOutput == ""){
			$MySQLObject = new MysqlUtilities($object->DatabaseName,$object->HtmlDebug,$object->Debug);
			$SelfProperties=get_object_vars($object);
			$SelfProperties = array_merge($this->getObjectAttributes($object),$SelfProperties);
			$MySQLObject->AddtoDB($object->TableName,$SelfProperties);
			if($thisOutput == ""){
				eval ("$"."object->set".$object->MAINCOLUMN."('".$MySQLObject->GetLastInsertID()."');");
			}
			$MySQLObject->Close();
		}
		return true;
	}
	/* update method for provided objects */	
	public function updateTableFromPostedData($object){
		//print_r($_POST);
		//var_dump($object);
		//eval("$"."object->set".$object->MAINCOLUMN."(".$_POST[$object->MAINCOLUMN].");");
		//echo $mainIdColumn."---".$thisOutput."#";
		$mainIdColumn =  "$"."thisOutput = $"."object->get".$object->MAINCOLUMN."();";
		eval($mainIdColumn);
		$Array = array();
		if($thisOutput != ""){
			//echo "updateTableFromPostedData";
			$MySQLObject = new MysqlUtilities($object->DatabaseName,$object->HtmlDebug,$object->Debug);
			$SelfProperties=$this->getObjectAttributes($object);
			//print_r($SelfProperties);
			//print_r($_POST);
			$profileColumns=$MySQLObject->GetColumns($object->TableName);
			//print_r($profileColumns);
			foreach($SelfProperties as $key => $value) { //Each variable is compared to variables of the object passed
				//echo $key.'-'.$value."-----\n";
				if(isset($value) && in_array(strtoupper($key),$profileColumns)){
					$Array[strtoupper($key)]=$value;
				}
			}
			//print_r($Array);die();
			$where_clause = $object->MAINCOLUMN ." = ".$thisOutput;
			$MySQLObject->UpdateDB($object->TableName,$Array,$where_clause);
		}
	}
	/* populate From ID method for provided objects */
	public function populateFromID($SelectColumns='*' , $Condition='',$object){
		$ExecuteQuery=true;
		/*if ($SelectColumns == '*' && !$AllVariablesSet)
		{
			$this->setAllVariablesSet(true);
			$ExecuteQuery=true;
		}
		elseif($SelectColumns == '*' && $AllVariablesSet)
			$ExecuteQuery=false;/**/
		$mainIdColumn =  "$"."thisOutput = $"."object->get".$object->MAINCOLUMN."();";
		eval($mainIdColumn);
		if($ExecuteQuery){
			$sql_statement = 'SELECT '.$SelectColumns.' FROM '.$object->TableName.' WHERE ID='.$thisOutput;
			if($Condition!='')
				$sql_statement .= ' and '.$Condition;
			$MySQLObject = new MysqlUtilities('',$object->HtmlDebug,$object->Debug);
			//echo $sql_statement;
			$sql_result = $MySQLObject->Query($sql_statement) or $MySQLObject->ShowError($sql_statement);
			if($sql_row = $MySQLObject->Row()){
				$object->setAllProperties($sql_row);
				return true;
			}
			else
				return false;
		}
		setIsModifyTrue();
	}
	/* delete method for provided objects */
	public function deleteDetails($object){
		//var_dump($object);
		$mainIdColumn =  "$"."thisOutput = $"."object->get".$object->MAINCOLUMN."();";
		eval($mainIdColumn);
		//echo $thisOutput;
		if( $thisOutput != "" ){
			$sql = "DELETE FROM $object->TableName where ". $object->MAINCOLUMN ." =".$thisOutput;
			$MySQLObject = new MysqlUtilities($this->DatabaseName,$object->HtmlDebug,$object->Debug);
			$MySQLObject->Query($sql) or $MySQLObject->ShowError($sql);
			$MySQLObject->Close();
		}
	}
	
	private function getObjectVariableValue($string="",$object){
		$mainIdColumn =  "$"."thisOutput = $"."object->get".$string."();";
		eval($mainIdColumn);
		return $thisOutput;	
	}
	public function getKeyValue($Column,$String='',$object){
		$KEY = $Column.$String;
		$mainIdColumn =  "$"."thisOutput = $"."object->getKeyValue($"."Column,$"."String);";
		eval($mainIdColumn);
		return $thisOutput;
	}
	public function getObjectAttributes($object){
		//echo $object->ColumnArray;
		$elements = explode(',',$object->ColumnArray);
		$selfArray;
		foreach($elements as $key => $value){
			$thisColumn =  "$"."thisOutput = $"."object->get".$value."();";
			try{
				if(method_exists($object,"get".$value)){
					eval($thisColumn);
					$selfArray[$value] = $thisOutput;
				}
			}catch(Exception $e){}
		}
		//print_r($selfArray);
		return $selfArray;
	}
	public function defaultOperation($object,$_Array){
		global $PageName;
		if(isset($_Array['Message'])){
			echo $_Array['Message']."<br>";
		}
		if(is_array(@$_Array['OperationString'])){
			foreach($_Array['OperationString'] as $key => $value){
				eval($value);
			}
		}
		
		if(isset($_Array['DefaultOperationString']) && $_Array['DefaultOperationString'] == "SearchListEdit"){
			echo generateTableEditLink($object->searchOperation($object->SearchArray,getValueGPC('Search')),0,'Edit',0,$PageName);
		}else if(isset($_Array['DefaultOperationString']) && $_Array['DefaultOperationString'] == "SearchList"){
			echo generateTable($object->searchOperation($object->SearchArray,getValueGPC('Search')),0,'Edit',0,$PageName);
		}else if(isset($_Array['DefaultOperationString'])){
			eval($_Array['DefaultOperationString']);
		}else{
			echo generateTableEditLink($object->searchOperation($object->SearchArray,getValueGPC('Search')),0,'Edit',0,$PageName);
		}
	}
	
	public function pagingCode($totalPages,$PARAMS){
		global $PageName;
		$SQLORDERBY = "";
			if(isset($_GET['SQLORDERBY'])){
				$SQLORDERBY = "&SQLORDERBY=".$_REQUEST['SQLORDERBY'];
				if($_GET['SQLORDERBYTYPE'] == "desc"){
					$SQLORDERBY .= "&SQLORDERBYTYPE=".$_REQUEST['SQLORDERBYTYPE'];
				}else{
					if(isset($_REQUEST['SQLORDERBYTYPE'])){ 
						$SQLORDERBY .= "&SQLORDERBYTYPE=".$_REQUEST['SQLORDERBYTYPE'];
					}else{
						$SQLORDERBY .= "&SQLORDERBYTYPE=asc";
					}
				}
			}else{
				
			}
			
		$TillPageNumber = ((getValueGPC('pageNumber')+5)> $totalPages) ? $totalPages :(getValueGPC('pageNumber')+5);
		$FromPageNumber = ((getValueGPC('pageNumber')-5)> 0) ?  (getValueGPC('pageNumber')-5) :1;
		$pagingCode ="";
		if(getValueGPC('pageNumber')>1)
		$pagingCode.=" <a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')-1)." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')-1)."');return false;\">&lt;PREV</a> | ";
		for($i=$FromPageNumber;$i<=$TillPageNumber;++$i){
		$pagingCode.="<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=$i onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=$i');return false;
		\">";
			if( getValueGPC('pageNumber') == $i ) {
			  $pagingCode.="<b><u>$i</b></u>";
			}else{ $pagingCode.="$i"; }
		$pagingCode.="</a> | ";
		}
		if((getValueGPC('pageNumber')+1)<=$totalPages)
		$pagingCode.="<a href=$PageName?checksum=".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')+1)." onclick=\"javascript:showData('$PageName','".getValueGPC('checksum')."&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber')+1)."');return false;\">NEXT&gt;</a> ";
		//$pagingCode .= "| <a href=\"$PageName?checksum=".getValueGPC('checksum')."&print_report=true&$PARAMS$SQLORDERBY\" target=\"_ReportPrint\">Print Report</a>  ";
		$pagingCode .= " | <a href=\"$PageName?checksum=".getValueGPC('checksum')."&print_page=true&$PARAMS$SQLORDERBY&pageNumber=".(getValueGPC('pageNumber'))."\" target=\"_ReportPrint\">Print Page</a>";
		return $pagingCode;
	}
	
}
?>