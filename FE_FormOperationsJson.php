<?php
	function generateFormJson($form,$formName,$onChange="#"){
		global $_DATA,$PageName,$Config;
		$Config = new get_config();
		$AJAXProcess =  true;
		$OnChnage="";
			$RowCount = 0;
		if($onChange == '#' && $onChange == ""){
			$OnChange = "javascript:document.form.submit();";
		}
		$outputForm=array();
		
		$outputForm['CONFIG']['MODE']=$form [0]['CONFIG']['MODE'];
		$outputForm['PAGE_NAME']=$PageName;
		$outputForm['PAGE_TITLE']=$PageTitle;
		$outputForm['ONCHANGE']=$onChange;
		$outputForm['CHECKSUM']=getValueGPC('checksum');
		
		for($i=0;$i<sizeof($form);$i++){
			$outputForm['ELEMENT'][$i] = $form[$i];
			$outputForm['ELEMENT'][$i]['VALIDATION'] = getClientSideValidation($form[$i]);
			$outputForm['ELEMENT'][$i]['OPERATION'] = getJsEvents($form[$i]);
			$outputForm['ELEMENT'][$i]['TITLE'] = FE_TitleDisplayFormat($form[$i]['TITLE']);
			$outputForm['ELEMENT'][$i]['TYPE'] = $form[$i]['TYPE'];
			$outputForm['ELEMENT'][$i]['NAME'] = $form[$i]['NAME'];
			$outputForm['ELEMENT'][$i]['VALUE'] = getValueGPC($form[$i]['NAME']);
			$outputForm['ELEMENT'][$i]['DEFAULT_VALUE'] = $form[$i]['VALUE'];
			if( isset($_DATA['Err_'.$form[$i]['NAME']])){ $outputForm['ELEMENT'][$i]['FE_ERROR'] = $_DATA['Err_'.$form[$i]['NAME']];}
		}
		@header('Cache-Control: no-cache, must-revalidate');
		@header('Content-type: application/json');
		$outputForm1[0]['FE_TYPE']='FORM';
		$outputForm1[0]['DATA']=$outputForm;
		return json_encode($outputForm1);
	}
	function getJsEvents($FormElement){
		$jsEvents = array();
		if(isset($FormElement['ONCHANGE'])){
			$jsEvents['ONCHANGE'] = "javascript:".$FormElement['ONCHANGE']."";
		}
		if(isset($FormElement['ONBLUR'])){
			$jsEvents['ONBLUR'] ="javascript:".$FormElement['ONCHANGE']."";
		}
		if(isset($FormElement['ONCLICK'])){
			$jsEvents['ONCLICK'] ="javascript:".$FormElement['ONCLICK']."";
		}
		if(isset($FormElement['UQCHK'])){
			$jsEvents['UQCHK'] ="javascript:checkUnique(this.value,'".$FormElement['UQCHK']['TABLE']."','".$FormElement['UQCHK']['COLUMN']."','".$FormElement['TITLE']."','".$FormElement['NAME']."','".getValueGPC('checksum')."')";
		}
		if(isset($FormElement['READONLY'])){
			$jsEvents['READONLY'] = "TRUE";
		}
		if(isset($FormElement['CLASS'])){
				$jsEvents['CLASS']=$FormElement['CLASS'];
		}else{ $jsEvents['CLASS'] = "";}
		if(isset($FormElement['ID'])){
				$jsEvents['ID']=$FormElement['ID'];
		}else{ $jsEvents['ID'] = "";} 
		if(isset($FormElement['STYLE'])){
				$jsEvents['STYLE']=$FormElement['STYLE'];
		}else{ $jsEvents['STYLE'] = "";}
		return $jsEvents;
	}
	function getClientSideValidation($FormElement){
		$clientSideValidation = array();
		if(isset($FormElement['MINVALUE'])){ $clientSideValidation['MINVALUE']=$FormElement['MINVALUE'];
		}if(isset($FormElement['MAXVALUE'])){ $clientSideValidation['MAXVALUE']=$FormElement['MAXVALUE'];
		}if(isset($FormElement['MINLENGTH'])){ $clientSideValidation['MINLENGTH']=$FormElement['MINLENGTH'];
		}if(isset($FormElement['MAXLENGTH'])){ $clientSideValidation['MAXLENGTH']=$FormElement['MAXLENGTH'];
		}if(isset($FormElement['ERROR_MINVALUE'])){ $clientSideValidation['ERROR_MINVALUE']=$FormElement['ERROR_MINVALUE'];
		}if(isset($FormElement['ERROR_MAXVALUE'])){ $clientSideValidation['ERROR_MAXVALUE']=$FormElement['ERROR_MAXVALUE'];
		}if(isset($FormElement['ERROR_MINLENGTH'])){ $clientSideValidation['ERROR_MINLENGTH']=$FormElement['ERROR_MINLENGTH'];
		}if(isset($FormElement['ERROR_MAXLENGTH'])){ $clientSideValidation['ERROR_MAXLENGTH']=$FormElement['ERROR_MAXLENGTH'];
		}if(isset($FormElement['ERROR_REQUIRED'])){ $clientSideValidation['ERROR_REQUIRED']=$FormElement['ERROR_REQUIRED'];
		}if(isset($FormElement['VALUE_TYPE'])){ $clientSideValidation['VALUE_TYPE']=$FormElement['VALUE_TYPE'];
		}if(isset($FormElement['VALIDDATE_PATTERN'])){ $clientSideValidation['VALIDDATE_PATTERN']=$FormElement['VALIDDATE_PATTERN'];
		}if(isset($FormElement['ERROR_VALUE_TYPE'])){$clientSideValidation['ERROR_VALUE_TYPE_SCRIPT'] = "var ".$FormElement['NAME']."_ERROR_VALUE_TYPE = '".$FormElement['ERROR_VALUE_TYPE']."'";}
		else{$clientSideValidation['ERROR_VALUE_TYPE_SCRIPT'] = "var ".$FormElement['NAME']."_ERROR_VALUE_TYPE = 'undefined'";}
		return $clientSideValidation;
	}
?>