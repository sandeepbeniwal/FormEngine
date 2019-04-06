<?php
$SlashAdded = false;
	function addSlashesinGPC(){
	global $SlashAdded;
		if(!(get_magic_quotes_gpc() && !$SlashAdded)){
		$SlashAdded = true;
			//Each variable is compared to variables of the object passed
			foreach($_POST as $key => $value){	
				$_POST[$key] = addslashes(trim($value));		
			}
			foreach($_GET as $key => $value){	//Each variable is compared to variables of the object passed
				$_GET[$key] = addslashes(trim($value));		
			}
		}
	}
	function str_makerand ($minlength=4, $maxlength=10, $useupper=0, $usespecial=0, $usenumbers=1){
		$charset="";
		$key="";
			$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}
	function getValueGPC($elementName,$value="",$type="",$source=""){
		global $_DATA,$SlashAdded;
		$valueToReturn="";
		if($type == 'C'){
			if(is_array($_REQUEST[$elementName])){
				foreach($_REQUEST[$elementName] as $_key => $_valueLocal){
					if($value == $_valueLocal){
						return $valueToReturn = "checked alt='$elementName $_key $_valueLocal $value'";
					}
				}
				return "";
			}
		}
		if(isset($_GET[$elementName]) && $source == "G"){
			$valueToReturn=$_GET[$elementName];
		}elseif(isset($_POST[$elementName])  && $source == "P"){
			$valueToReturn=$_POST[$elementName];
		}elseif(isset($_DATA[$elementName])  && $source == "D"){
			$valueToReturn=$_DATA[$elementName];
		}elseif(isset($_GET[$elementName]) ){
			$valueToReturn=$_GET[$elementName];
		}elseif(isset($_POST[$elementName]) ){
			$valueToReturn=$_POST[$elementName];
		}elseif(isset($_DATA[$elementName]) ){
			$valueToReturn=$_DATA[$elementName];
		}
		
		if($type == 'R' && $valueToReturn == $value){ $valueToReturn = "selected";}
		elseif($type == 'C' && $valueToReturn == $value){$valueToReturn = "checked";}
		elseif($type == 'S' && $valueToReturn == $value){$valueToReturn = "selected";}
		if($valueToReturn == 'NULL'){$valueToReturn = "";}
		if(get_magic_quotes_gpc() || $SlashAdded){
			$valueToReturn = stripslashes($valueToReturn);
		}
		
		return $valueToReturn;
	}
	
	function queryToOptions($_sql,$addOneEmpty = false){
			includeOnce("lib/include.php"); 
		$optionArray =array();

		global $db_name;
		$Config = new get_config();
		//var_dump($Config);
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		
		@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
		//echo $Config->db_name;
		$sql=$_sql;//"select COUNTRY,NAME from COUNTRY order by NAME";
		$res=mysql_query($sql) or die($sql.mysql_error());
		$i=0;
		if($addOneEmpty == true){
			$optionArray[$i]['VALUE']  = "";
			$optionArray[$i]['LABEL']  = "All";
			$i++;
		}

		while($line = mysql_fetch_array($res)){
			$optionArray[$i]['VALUE']  = $line[0];
			$optionArray[$i]['LABEL']  = $line[1];
			++$i;
		}
		return $optionArray;

	}
	function query2options($_sql,$addOneEmpty = false){
				includeOnce("lib/include.php"); 
		$optionArray =array();

		global $db_name;
		$Config = new get_config();
		//var_dump($Config);
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());
		
		@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
		//echo $db_name;
		$sql=$_sql;//"select COUNTRY,NAME from COUNTRY order by NAME";
		$res=mysql_query($sql) or die($sql.mysql_error());
		$i=0;
		if($addOneEmpty == true){
			$optionArray[$i]['VALUE']  = "";
			$optionArray[$i]['LABEL']  = "All";
			$i++;
		}

		while($line = mysql_fetch_assoc($res)){
			$j=0;
			foreach($line as $key => $value){
				if($j == 0 ){
					$optionArray[$i]['VALUE'] = $value;
					$optionArray[$i][$key] = $value;
				}else if($j == 1 ){
					$optionArray[$i]['LABEL'] = $value;
					$optionArray[$i][$key] = $value;
				}else{
					$optionArray[$i][$key] = $value;
				}
				++$j;
			}
//			$optionArray[$i]['VALUE']  = $line[0];
//			$optionArray[$i]['LABEL']  = $line[1];
			++$i;
		}
		return $optionArray;

	}
	function generateJSONTableData($Array){
		//print_r($Array);
		$returnData = "";
		$addedMain = false;
		$arrayIndex = array();
		$i=0;
		foreach($Array as $key => $value){
			if($i>0){
				if($addedMain){$returnData .=",";}
				$returnData .="{";
				if(is_array($value)){
					$addedMain1=false;
					$returnData .="\"$key\":{";
					foreach($value as $key1 => $value1){
						if($addedMain1){$returnData .=",";}
						$returnData .="\"".$arrayIndex[$key1]."\":\"$value1\"";
						$addedMain1 = true;
					}
					$returnData .="}";	
				}else{
					$returnData .="\"$key\":\"$value\"";
				}
				$addedMain = true;
				$returnData .="}";
			}else{
				foreach($value as $key1 => $value1){
					$arrayIndex[$key1] = $value1;
				}
				++$i;
			}
		}
		return $returnData;
		
	}
	function generateSuggessionJSONCustom($Array){
		echo json_encode($Array);
	}
	function generateSuggessionJSON($Array){
		//print_r($Array);
		$returnData = "";
		$addedMain = false;
		$arrayIndex = array();
		$arrayIndex[0]="id";
		$arrayIndex[1]="label";
		$arrayIndex[2]="value";
		$i=0;
		$returnData .="[";
		foreach($Array as $key => $value){
			if($i>0){
				if($addedMain){$returnData .=",";}
				//$returnData .="{";
				if(is_array($value)){
					$addedMain1=false;
					//$returnData .="\"$key\":";
					$returnData .="{";
					foreach($value as $key1 => $value1){
						if($addedMain1){$returnData .=",";}
						$returnData .="\"".$arrayIndex[$key1]."\":\"$value1\"";
						$addedMain1 = true;
					}
					$returnData .="}";	
				}else{
				//	$returnData .="\"$key\":\"$value\"";
				}
				$addedMain = true;
				//$returnData .="}";
			}else{
				foreach($value as $key1 => $value1){
					//$arrayIndex[$key1] = $value1;
				}
				++$i;
			}
		}
		$returnData .="]";
		return $returnData;
		
	}
	function getLabelForDisplay($_VALUE,$_TABLE,$_COLUMN,$ElementName=''){
		//debug_print_backtrace();
		//if(file_exists('lib/include.php'))
		includeOnce("lib/include.php");
		global $db_name;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());	
		@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
		$pathinf = pathinfo($_SERVER['SCRIPT_FILENAME']);
		//echo "\"".$_TABLE.$_COLUMN.".class.php\"";
		
		include_once($pathinf['dirname']."/".$_TABLE.".class.php");
		eval('$'.$_TABLE.' = new '.$_TABLE.'("",false, false , false);');
		eval('$resArr = $'.$_TABLE.'->searchOperation($'.$_TABLE.'->MAINCOLUMN.",'.$_COLUMN.'","'.$_VALUE.'");');
		//print_r($resArr);
		if(is_array($resArr))
		$_POST[$ElementName.'_disp'] = $resArr[1][1];
			
	}
	function fileList($FolderPath,$lookUpPath="",$dbCheck="false",$tableName ="",$fieldName=""){
		$availableList = array();
		if($dbCheck == true || $dbCheck == "true" || $dbCheck == TRUE || $dbCheck == "TRUE"){
			
			includeOnce("lib/include.php"); 
			global $db_name;
			$Config = new get_config();
			$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());	
			@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
			$sql = "select $fieldName from $tableName";
			$res=mysql_query($sql);// or die($sql.mysql_error());
			$j=0;
			while($line = mysql_fetch_array($res)){
				$availableList[$j]=$line[$fieldName];
				$j++;
			}
		}
		$optionArray =array();
		$count = 0;
		$dir = $FolderPath;
		$i = 0;
		if (is_dir ( $dir )) {
			$files = scandir ( $dir );
			foreach ( $files as $key => $value ) {
				if (! ($value == "." || $value == "..")) {
					if(!in_array($value,$availableList)){
						$optionArray[$i]['VALUE']  = $lookUpPath.$value;
						$optionArray[$i]['LABEL']  = $value;
						++$i;
					}
				}
			}
		}
		return $optionArray;	
	}
	function setDataTabLabel($Label){
		echo "<script type=\"text/javascript\">
		document.getElementById('HeaderText').innerHTML = '$Label';
		</script>";
	}
	function getQueryResult($sql){
		global $db_name;
			includeOnce("lib/include.php"); 
			$Config = new get_config();
			$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());	
			@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
			//$sql = "select $fieldName from $tableName";
			//echo $sql;
			$res=mysql_query($sql)or die($sql.mysql_error());
			$result =array();
			while($line = mysql_fetch_array($res)){
				$result[]=$line;
			}
			return $result;
	}
	function getQueryResultAssoc($sql){
		global $db_name;
			includeOnce("lib/include.php"); 
			$Config = new get_config();
			$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());	
			@mysql_select_db($Config->db_name) or die($Config->db_name."-2".mysql_error());
			//$sql = "select $fieldName from $tableName";
			//echo $sql;
			$res=mysql_query($sql)or die($sql.mysql_error());
			$result =array();
			while($line = mysql_fetch_assoc($res)){
				$result[]=$line;
			}
			return $result;
	}
	function checkUniqueAjax($value,$tableName,$fieldName,$ACTIVATED = false){
			
			global $db_name;
			$Config = new get_config();
			$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("1".mysql_error());	
			@mysql_select_db($Config->db_name) or die($db_name."-2".mysql_error());
			$sql = "select $fieldName from $tableName where $fieldName = '$value'";
			if($ACTIVATED){
				$sql = "select $fieldName from $tableName where $fieldName = '$value' and ACTIVE = 'Y' ";
			}
			$res=mysql_query($sql)or die($sql.mysql_error());
			$result =array();
			if($line = mysql_fetch_array($res)){
				return "1";
			}else{
				return "0";
			}
	}
	function queryToHTMLOptions($_sql, $_ElementValue = "",$addOneEmpty = false) {
		if(@$_REQUEST['debug']=='fe-debug'){debug_print_backtrace();}
		global $db_name;
		$Config = new get_config();
		$conn = mysql_connect($Config->db_host,$Config->db_user,$Config->db_pass,$Config->db_name) or die("in queryToHTMLOptions ".mysql_error());	
		@mysql_select_db($Config->db_name) or die($db_name."-2 in queryToHTMLOptions ".mysql_error());			
		$sql = $_sql;
		$res = mysql_query ( $sql ) or die ( $sql . mysql_error () );
		$i = 0;
		$returnOptionsHtml = "";
		if($addOneEmpty == true){
			$returnOptionsHtml = "<option value=''>Select </option>";
		}
		
		if(mysql_num_rows(@$res)<= 0 ){
			//	 $returnOptionsHtml .= "<option value='-1'>Not Available</option>\n";;
		}else{
			//generating option html string using db values
			while ( @$line = @mysql_fetch_array ( @$res ) ) {
				$extString = "";
				if ($_ElementValue == $line [0]) {
					$extString = "selected";
				}
				$returnOptionsHtml .= "<option value='" . $line [0] . "' $extString>" . stripslashes($line [1]) . "</option>\n";
			}
		}
	
		return $returnOptionsHtml;
	}
	function getFormById($formId){
		 $result = getQueryResult('select * from uls_forms where FORM_ID = '.$formId);
		 return $result[0];//['FORM_DETAILS'];
	}
	function getFormByTableName($formId){
		 $result = getQueryResult('select * from uls_forms where TABLE_NAME = "'.$formId.'"');
		return $result[0];//['FORM_DETAILS'];
	}

	function generateClass($tableName,$extends = 'Validate'){
		$sql = "DESCRIBE  $tableName";
		$result = getQueryResult($sql);
		//echo "<pre>";
		//print_r($result);
		$codeString = "include_once('MysqlUtilities.class.php');\n

	include_once('$extends.class.php');\n if(file_exists('db_config.php'))\n	include_once('db_config.php');\n
	if(file_exists('FE_CRUD.php'))\n	include_once('FE_CRUD.php');\n	global \$db_name,\$formObject;\n

	class $tableName  extends $extends { \n";
	$columnArray = "";$SearchArray="";
	for($i=0;$i<sizeof($result);++$i){
		$dataType = "";$maxLength = 0;
		if($result[$i]['Type'] == "bigint(20)" ){
			$dataType = "int";
		}else if(substr($result[$i]['Type'],0,7) == 'varchar'){
			$dataType = "varchar";
			$maxLength = substr($result[$i]['Type'],8,-1);
		}else if(substr($result[$i]['Type'],0,4) == 'date' || substr($result[$i]['Type'],0,4) == 'time'){
			$dataType = "date";
			$maxLength = substr($result[$i]['Type'],8,-1);
		}
		$codeString .= "private $".$result[$i]['Field'].";\n";
		$codeString .= "private $".$result[$i]['Field']."_IS_NULLABLE = \"".$result[$i]['Null']."\";\n";
		$codeString .= "private $".$result[$i]['Field']."_COLUMN_DEFAULT = \"".$result[$i]['Default']."\";\n";
		$codeString .= "private $".$result[$i]['Field']."_DATA_TYPE = \"".$dataType."\";\n";
		$codeString .= "private $".$result[$i]['Field']."_CHARACTER_MAXIMUM_LENGTH =\"".$maxLength ."\";\n";
		$codeString .= "private $".$result[$i]['Field']."_NUMERIC_PRECISION = 10;\n";
		$codeString .= "private $".$result[$i]['Field']."_CHARACTER_MINIMUM_LENGTH ;\n";
		if($i < sizeof($result)-1){
			$columnArray .= $result[$i]['Field'].",";
			if($result[$i]['Field'] != "ACTIVE" && $result[$i]['Field'] != "UPDTTM" && $result[$i]['Field'] != "USER_ID" && $result[$i]['Field'] != "BU_ID" && $result[$i]['Field'] != "ORG_ID"  ){
				$SearchArray .=$result[$i]['Field'].",";
			}
		}else{
			$columnArray .= $result[$i]['Field']."";
			if($result[$i]['Field'] != "ACTIVE" && $result[$i]['Field'] != "UPDTTM" && $result[$i]['Field'] != "USER_ID" && $result[$i]['Field'] != "BU_ID" && $result[$i]['Field'] != "ORG_ID"  ){
				$SearchArray .=$result[$i]['Field']."";
			}
		}
	}
	$columns = explode(",",$columnArray);
	$codeString .= "public \$MAINCOLUMN = \"".$columns[0]."\";\n";
	$codeString .= "public \$ColumnArray=\"$columnArray\";\n";
	$codeString .= "public \$SearchArray=\"ID\";\n";
	$codeString .= "public \$SearchArrayExtended=\"$columnArray\";\n";
	$codeString .= "\n"."public function getColumnArray(){\n\t	return \$"."this->ColumnArray;	\n}\n	public function getSearchArray(){\n\t	return \$this->SearchArray;	\n}\n	private \$IsModify;\n	private \$MainAdminId;\n	public \$TableName = \"$tableName\";\n	public \$DatabaseName =  \"\";\n	public \$AllVariablesSet=\"\";\n	public \$HtmlDebug;\n	public \$Debug;\n	public function setIsModifyTrue(){\n\t		\$"."this->IsModify = true; \n}\n";
	foreach($columns as $key => $value){
		$codeString .= "\n public function get".$value."(){\n\t return \$this->$value;\n}\n public function set$value(\$value){\n\t	return \$this->$value = trim(\$value);\n}\n";
	}
	$codeString .=<<<start
	public function __construct(\$_ID="", \$ajax=false, \$HtmlDebug=false , \$Debug=false){
	 global \$db_name;
	\$this->DatabaseName =  \$db_name;
	if(\$_ID != "")		\$this->ID = \$_ID;
		\$this->AllVariablesSet = false;
		\$this->HtmlDebug = \$HtmlDebug;
		\$this->Debug = \$Debug;
		\$this->TableName = "$tableName";
		\$this->IsModify = false;
		if(\$ajax){}
	}
	public function setAllProperties(\$Object='',\$Array=''){
		\$SelfProperties=get_object_vars(\$this);	//Gets variables of this class
		if(\$Object!=''){
			\$ReceivedProperties=get_object_vars(\$Object);
		}else{
			\$ReceivedProperties=\$Array;
		}
		foreach(\$SelfProperties as \$key => \$value)	//Each variable is compared to variables of the object passed
		{
			foreach(\$ReceivedProperties as \$newkey => \$newvalue)
			{
				if(strtoupper(\$key) == strtoupper(\$newkey))//if a match is found, this object's vars are populated;
				{
					\$this->\$key = trim(\$newvalue);
					break;
				}					
			}
		}
		if(\$this->HtmlDebug)
		{
			echo "\n<!--";
			print_r(\$this);
			echo "-->";
		}
		elseif(\$this->Debug)
		{
			echo "<br>";
			print_r(\$this);
		}
	}
	public function populateFromID(\$SelectColumns='*' , \$Condition='')
	{
		\$ExecuteQuery=true;
		/*if (\$SelectColumns == '*' && !\$AllVariablesSet)
		{
			\$this->setAllVariablesSet(true);
			\$ExecuteQuery=true;
		}
		elseif(\$SelectColumns == '*' && \$AllVariablesSet)
			\$ExecuteQuery=false;/**/
		if(\$ExecuteQuery)
		{
			\$sql_statement = 'SELECT '.\$SelectColumns.' FROM '.\$this->TableName.' WHERE '. \$this->MAINCOLUMN .' ='.\getValueGPC(\$this->MAINCOLUMN);
			if(\$Condition!='')
				\$sql_statement .= ' and '.\$Condition;
			\$MySQLObject = new MysqlUtilities('',\$this->HtmlDebug,\$this->Debug);
			\$sql_result = \$MySQLObject->Query(\$sql_statement) or \$MySQLObject->ShowError(\$sql_statement);
			if(\$sql_row = \$MySQLObject->Row())
			{
				\$this->setAllProperties(\$sql_row);
				return true;
			}
			else
				return false;
		}
		setIsModifyTrue();
	}
	public function validateDBCompatibility(\$Column='')
	{
		\$ERROR = false;
		if(\$this->getKeyValue(\$Column) == NULL and \$this->getKeyValue(\$Column,"_IS_NULLABLE") == "NO"){ 
			\$ERROR = true;
		}
		else if(\$this->getKeyValue(\$Column,"_DATA_TYPE") == "varchar" && \$this->getKeyValue(\$Column,"_DATA_TYPE") == "char" && \$this->getKeyValue(\$Column,"_DATA_TYPE") == "text")
		{
			if(\$this->getKeyValue(\$Column,"_CHARACTER_MAXIMUM_LENGTH") > strlen(\$this->getKeyValue(\$Column)))
			{
				\$ERROR = true;
			}
			if(\$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") !=null && \$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") >0)
			{
				if(\$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") < strlen(\$this->getKeyValue(\$Column)))
				{
					\$ERROR = true;
				}
			}
		}
		else if(\$this->getKeyValue(\$Column,"_DATA_TYPE") == "int" && \$this->getKeyValue(\$Column,"_DATA_TYPE") == "bigint")
		{
			if (!(ereg("^([0-9])+$", \$this->getKeyValue(\$Column))))
			{
				\$ERROR = true;
			}
			if(\$this->getKeyValue(\$Column,"_NUMERIC_PRECISION") > strlen(\$this->getKeyValue(\$Column)))
			{
				\$ERROR = true;
			}
			if(\$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") !=null && \$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") >0)
			{
				if(\$this->getKeyValue(\$Column,"_CHARACTER_MINIMUM_LENGTH") < strlen(\$this->getKeyValue(\$Column)))
				{
					\$ERROR = true;
				}
			}
		}
		return \$ERROR;
	}


	public function getKeyValue(\$Column,\$String='') { 
		\$KEY = \$Column.\$String;
		return \$this->\$KEY;
	}
	public function insertIntoTable(\$table=''){
		\$gitFeCrudOps = new gitFeCrudOps();
		if(\$table != "")
			return \$gitFeCrudOps->insertIntoTable(\$table,\$this);
		else
			return \$gitFeCrudOps->insertIntoTable(\$this->TableName,\$this);
	}
	public function searchOperation(\$SelectColumns = '*' , \$SearchTerm=''){
		\$gitFeCrudOps = new gitFeCrudOps();
		return \$gitFeCrudOps->searchOperation(\$SelectColumns,\$SearchTerm,\$this);
	}
	public function deleteDetails(){
		\$gitFeCrudOps = new gitFeCrudOps();
		return \$gitFeCrudOps->deleteDetails(\$this);
	}
	public function updateTableFromPostedData(){
		\$gitFeCrudOps = new gitFeCrudOps();
		return \$gitFeCrudOps->updateTableFromPostedData(\$this);
	}
	public function operations(\$Operation,\$_Array){
		\$gitFeCrudOps = new gitFeCrudOps();
		\$evalString =  "$"."thisOutPut = "."$"."gitFeCrudOps->".\$Operation."($"."this,$"."_Array);";
		if(method_exists(\$gitFeCrudOps,\$Operation)){
			eval(\$evalString);
			return \$thisOutPut;
		}else{
		return "MethodNotAvailable";
		}
	}
start;
	$codeString .= "} 
		\$formObject = new $tableName(\"\",false, false , false);
		//var_dump(\$formObject);";
	//echo "<pre>".($codeString);
	eval ($codeString);
	}
if(!function_exists('execInBackground')){
	function execInBackground($cmd) { 
		if (substr(php_uname(), 0, 7) == "Windows"){ 
			pclose(popen("start /B ". $cmd, "r"));  
		} 
		else { 
			exec($cmd . " > /dev/null &");   
		}
	} 
}
if(!function_exists('convert_number')){
	function convert_number($number){
		if (($number < 0) || ($number > 999999999)){
			throw new Exception("Number is out of range");
		}
	 
		$Gn = floor($number / 100000);  /* Millions (giga) */
		$number -= $Gn * 100000;
		$kn = floor($number / 1000);     /* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);      /* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);       /* Tens (deca) */
		$n = $number % 10;               /* Ones */
	 
		$res = "";
	 
		if ($Gn){
			$res .= convert_number($Gn) . " Lacs";
		}
	 
		if ($kn){
			$res .= (empty($res) ? "" : " ") .
			convert_number($kn) . " Thousand";
		}
	 
		if ($Hn){
			$res .= (empty($res) ? "" : " ") .
			convert_number($Hn) . " Hundred";
		}
	 
		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six","Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen","Nineteen");
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty","Seventy", "Eigthy", "Ninety");
	 
		if ($Dn || $n){
			if (!empty($res)){	
				$res .= " and ";
			}
	 
			if ($Dn < 2){
				$res .= $ones[$Dn * 10 + $n];
			}else{
				$res .= $tens[$Dn];
	 
				if ($n){
					$res .= "-" . $ones[$n];
				}
			}
		}
		if (empty($res)){
			$res = "zero";
		}
		return $res;
	}
}
?>