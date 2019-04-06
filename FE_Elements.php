<?php
	
	function generateFormElement($formElement,$formName,$onChange="#",$TableRowStartTag='<tr>',$TableRowEndTag="</tr>",$TableColStartTag='td',$TableColEndTag='/td',$TableLabelColStartTag='td align=right valign=top',$TableLabelColEndTag='/td',$LabelGroup = false){
		global $isApiCall;
		if($isApiCall){
				return generateFormJson($form,$formName,$onChange);
		}
		global $_DATA,$PageName,$Config;
		if(getValueGPC('print_page') == "" | getValueGPC("print_report") == "" && getValueGPC('pageNumber') == "" && getValueGPC('excel_export') == ""){
		}else{ return ;}
		includeOnce("config/config.class.php");
		$Config = new get_config();
		$AJAXProcess =  true;
		$html ='';//= "\n<table class=\"git_table table\">";
		$OnChnage="";
		$RowCount = 0;
		if($onChange == '#' && $onChange == ""){
			$OnChnage = "onchange=\"javascript:document.form.submit();\"";
		}
		$class;$ID;$STYLE;$astrixMand; 
		$htmlFormTinyMCEProcess = "";
		$phpTourId =1;
		$i=0;
		$form[0]=$formElement;
		if(sizeof($form[$i])>0){
			
			/*******Form Design Logic***/
		$clientSideValidation = '';
		$required = '';
		if(isset($form[$i]['MINVALUE'])){ $clientSideValidation .= ' MINVALUE="'.$form[$i]['MINVALUE'].'" ';
		}if(isset($form[$i]['MAXVALUE'])){ $clientSideValidation .= ' MAXVALUE="'.$form[$i]['MAXVALUE'].'" ';
		}if(isset($form[$i]['MINLENGTH'])){ $clientSideValidation .= ' MINLENGTH="'.$form[$i]['MINLENGTH'].'" ';
		}if(isset($form[$i]['MAXLENGTH'])){ $clientSideValidation .= ' MAXLENGTH="'.$form[$i]['MAXLENGTH'].'" ';
		}if(isset($form[$i]['ERROR_MINVALUE'])){ $clientSideValidation .= ' ERROR_MINVALUE="'.$form[$i]['ERROR_MINVALUE'].'" ';
		}if(isset($form[$i]['ERROR_MAXVALUE'])){ $clientSideValidation .= ' ERROR_MAXVALUE="'.$form[$i]['ERROR_MAXVALUE'].'" ';
		}if(isset($form[$i]['ERROR_MINLENGTH'])){ $clientSideValidation .= ' ERROR_MINLENGTH="'.$form[$i]['ERROR_MINLENGTH'].'" ';
		}if(isset($form[$i]['ERROR_MAXLENGTH'])){ $clientSideValidation .= ' ERROR_MAXLENGTH="'.$form[$i]['ERROR_MAXLENGTH'].'" ';
		}if(isset($form[$i]['ERROR_REQUIRED'])){ $clientSideValidation .= ' ERROR_REQUIRED="'.$form[$i]['ERROR_REQUIRED'].'" ';
		}if(isset($form[$i]['VALUE_TYPE'])){ $clientSideValidation .= ' VALUE_TYPE="'.$form[$i]['VALUE_TYPE'].'"';
		}if(isset($form[$i]['VALIDDATE_PATTERN'])){ $clientSideValidation .= ' VALIDDATE_PATTERN="'.$form[$i]['VALIDDATE_PATTERN'].'" ';
		}
		if(isset($form[$i]['ERROR_VALUE_TYPE'])){echo "<script> var ".$form[$i]['NAME']."_ERROR_VALUE_TYPE = \"".$form[$i]['ERROR_VALUE_TYPE']."\"</script>";}
		else{//echo "<script> var ".$form[$i]['NAME']."_ERROR_VALUE_TYPE = \"undefined\"</script>";
		}
		$changeEvent = "";$READ_ONLY="";$tourEvent = '';
		if(isset($form[$i]['ONCHANGE'])){
			$changeEvent = "onchange=\"javascript:".$form[$i]['ONCHANGE']."\"";
			$changeEvent .= "onblur=\"javascript:".$form[$i]['ONCHANGE']."\"";
		}
		if(isset($form[$i]['ONCLICK'])){
			$changeEvent .= " onclick=\"javascript:".$form[$i]['ONCLICK']."\" ";
		}
		if(isset($form[$i]['UQCHK'])){
			$changeEvent .="onBlur=\"javascript:checkUnique(this.value,'".$form[$i]['UQCHK']['TABLE']."','".$form[$i]['UQCHK']['COLUMN']."','".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."','".$form[$i]['NAME']."','".getValueGPC('checksum')."')\"";
		}
		if(isset($form[$i]['TOUR_TEXT'])){
			$tourEvent = " tour_for = '".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."' tour_text='".addslashes($form[$i]['TOUR_TEXT'])."' ";
			$form[$i]['CLASS'] = $form[$i]['CLASS']." tour_cls ";
			echo "<script>$('#".$form[$i]['NAME']."').attr('tour_no',optionClicked+".++$phpTourId.");</script>";
		}
		if(isset($form[$i]['READONLY'])){
			$READ_ONLY = " readonly=\"readonly\" ";
		}
		if(isset($form[$i]['CLASS'])){
				$class = " class =\"".$form[$i]['CLASS']."\" ";
			}else{ $class = "";}
		if(isset($form[$i]['ID'])){
				$ID = " ID =\"".$form[$i]['ID']."\" ";
			}else{ $ID = "";} 
		if(isset($form[$i]['STYLE'])){
				$STYLE = " STYLE =\"".$form[$i]['STYLE']."\" ";
			}else{ $STYLE = "";} 
		if(isset($form[$i]['ERROR_REQUIRED'])){
			//$astrixMand = " <font style=\"color:red\">*</font>";
			$required = 'required';
		}else{$astrixMand = '';}
		if (@$form [0] ['CONFIG'] ['MODE'] == "4") {
			$colspan = 6;
			if ($RowCount % 2 == 0) {
				$TableRowStart = $TableRowStartTag;//"<tr>";
				$TableRowEnd = "";
				++ $RowCount;
			} else {
				$TableRowStart = "";
				$TableRowEnd = $TableRowEndTag;//"</tr>";
				++ $RowCount;
			}
		} else {
				$colspan = 2;
				$TableRowStart = $TableRowStartTag;//"<tr>";
				$TableRowEnd = $TableRowEndTag;//"</tr>";
		}
		if($form [$i] ['TYPE'] == "HTMLEDITOR"){
			$RowCount = 0;
		}
		if ($form [$i] ['TYPE'] == "HEADING" || $form [$i] ['TYPE'] == "EMPTY" || $form [$i] ['TYPE'] == "HIDDEN" || $form [$i] ['TYPE'] == "SUBMIT_RESET") {
			$RowCount = 0;
		}
		
		if ($form [$i] ['TYPE'] == "HEADING") {
			$KEY = $form [0] ['CONFIG'] ['TITLE_KY'];
			$KEY = $form [0] ['CONFIG'] ['LANG'] [$KEY]; //['REGISTER_BY'];
			//print_r($KEY); 
			$Display_value = "";
			if (isset ( $KEY [$form [$i] ['NAME']] )) {
				$Display_value = $KEY [$form [$i] ['NAME']];
			} else {
				$Display_value = $form [$i] ['NAME'];
			}
			$html .= "\n$TableRowStartTag <$TableColStartTag $class $STYLE align=Left valign=top colspan=$colspan >&nbsp;&nbsp;<b>
			    	" . $Display_value . "</b><$TableColEndTag>$TableRowEndTag";
			//continue;
		}
		if ($form [$i] ['TYPE'] == "EMPTY") {
			$html .= "\n$TableRowStartTag <$TableColStartTag class='col-md-12 control-label' align=Left valign=top colspan=$colspan >&nbsp;<$TableColEndTag> $TableRowEndTag";
			//continue;
		}
		/*******************************/
		//$labelHtml = "<$TableLabelColStartTag  for='".$form[$i]['NAME']."'><span>".FE_TitleDisplayFormat(@$form[$i]['TITLE']).": </span><$TableLabelColEndTag>";
		$LabelinGroup = '';
		$LabelinSeperate = '';
		if($LabelGroup){
			$LabelinGroup = $labelHtml;
		}else{
			$LabelinSeperate = $labelHtml;
		}
			if ($form[$i]['TYPE'] == "SUBMIT_RESET" || $form[$i]['TYPE'] == "SUBMIT" || $form[$i]['TYPE'] == "BUTTON"  || $form[$i]['TYPE'] == "ANCHOR"){
				$_t = '';if($form[$i]['TYPE'] == "ANCHOR" && $TableColEndTag != '/td'){
					$_t="<$TableColStartTag>&nbsp;<$TableColEndTag>";
				}
		    	$html.="\n$TableRowStart\n\t$_t<$TableColStartTag class='col-sm-6 col-lg-3' align=center valign=top colspan=2>";
		    }elseif($form[$i]['TYPE'] == "HTMLEDITOR"){
				$html.="\n$TableRowStartTag\n\t$LabelinSeperate\n\t<$TableColStartTag align=left valign=top $class $STYLE  colspan=3>$LabelinGroup\n\t\t";
			}elseif($form[$i]['TYPE'] != "HIDDEN"){
				$html.="\n$TableRowStart\n\t$LabelinSeperate\n\t<$TableColStartTag align=left valign=top $STYLE>$LabelinGroup\n\t\t";}
		    
			if($form[$i]['TYPE'] == "ANCHOR"){
				$html .="<a name='".$form[$i]['NAME']."' ctrlval='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."' href='".$form[$i]['HREF']."' target='".$form[$i]['TARGET']."'>".$form[$i]['VALUE']."</a>";
			}elseif($form[$i]['TYPE'] == "TEXT"){
				$html .="<input type=text placeholder='".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."' name='".$form[$i]['NAME']."' ctrlval='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."' $required>";
			}elseif($form[$i]['TYPE'] == "PASSWORD"){
				$html .="<input type=password placeholder='".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."' name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."'>";
			}elseif($form[$i]['TYPE'] == "SELECTSUGGEST"){
				if( getValueGPC($form[$i]['NAME']) !="" && getValueGPC($form[$i]['NAME']."_disp") == "" ){
					getLabelForDisplay(getValueGPC($form[$i]['NAME']),$form[$i]['TABLE'],$form[$i]['COLUMN'] !='' ? $form[$i]['COLUMN']:$form[$i]['NAME'],$form[$i]['NAME']);				
				}
				$html .="<input type=text placeholder='".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."' target='".$form[$i]['NAME']."' name='".$form[$i]['NAME']."_disp' id='".$form[$i]['NAME']."_disp' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME']."_disp")."' autocomplete='off' class='autocomplete' action='".$form[$i]['ACTION']."'>";				
				$html .="<input type=hidden name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."' autocomplete='off' >";	
				$html .= '<script>autoSuggestFE();</script>';
			}elseif($form[$i]['TYPE'] == "TAGSUGGEST"){
				if( getValueGPC($form[$i]['NAME']) !="" && getValueGPC($form[$i]['NAME']."_disp") == "" ){
					getLabelForDisplay(getValueGPC($form[$i]['NAME']),$form[$i]['TABLE'],$form[$i]['COLUMN'] !='' ? $form[$i]['COLUMN']:$form[$i]['NAME'],$form[$i]['NAME']);				
				}
				$html .="<input type=text placeholder='".FE_TitleDisplayFormat(@$form[$i]['TITLE'])."' target='".$form[$i]['NAME']."' name='".$form[$i]['NAME']."_disp' id='".$form[$i]['NAME']."_disp' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME']."_disp")."' autocomplete='off' class='autocomplete tagcomplete' action='".$form[$i]['ACTION']."'>";				
				$html .="<div class='git_topicMain git_topicMain".$form[$i]['NAME']."'></div>";	
				$html .= "<script>autoSuggestFE();tagSuggestFE('".$form[$i]['NAME']."');</script>";
			}elseif($form[$i]['TYPE'] == "PHONE"){
				$html .="<input type=text name='".$form[$i]['NAME']."_CODE' id='".$form[$i]['NAME']."_CODE' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME']."_CODE")."' style=\"width:50px\" > <input type=TEXT name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."' style=\"max-width:100px !important\" size=15 $required>";
			}elseif($form[$i]['TYPE'] == "TITLE_NAME"){
				$html .="<select name='".$form[$i]['NAME']."_NAME_TITLE' id='".$form[$i]['NAME']."_NAME_TITLE'>
					<option value='Mr.'>Mr.</option>
					<option value='Mrs.'>Mrs.</option>
					<option value='Miss.'>Miss</option>
					<option value='Dr.'>Dr.</option>
					<option value='Er.'>Er.</option>
				</select>";
			//$html .="	<input type=text name='".$form[$i]['NAME']."_NAME_TITLE' id='".$form[$i]['NAME']."_NAME_TITLE' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'].'_NAME_TITLE')."' size=3> ";
			$html .="<input type=text placeholder='First Name' name='".$form[$i]['NAME']."_FIRST_NAME' id='".$form[$i]['NAME']."_FIRST_NAME' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'].'_FIRST_NAME')."' size=12 placeholder='First Name'>"."<input type=text name='".$form[$i]['NAME']."_LAST_NAME' id='".$form[$i]['NAME']."_LAST_NAME' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'].'_LAST_NAME')."' size=12 placeholder='Last Name'>";
				
			}elseif($form[$i]['TYPE'] == "FILE"){
				$html .="<input type=file name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE >
				<input type=\"hidden\" name='".$form[$i]['NAME']."_TYPE'>
				<input type=\"hidden\" name='".$form[$i]['NAME']."_SIZE'>
				";
			}elseif($form[$i]['TYPE'] == "HIDDEN" && getValueGPC($form[$i]['NAME']) != ""){
				$html .="<input type=HIDDEN name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."'>";
			}elseif($form[$i]['TYPE'] == "HIDDEN" ){
				$html .="<input type=HIDDEN name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".@$form[$i]['VALUE']."'>";
			}elseif($form[$i]['TYPE'] == "TEXTAREA"){
				$html .="<textarea name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' placeholder='".$form[$i]['TITLE']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE>".getValueGPC($form[$i]['NAME'])."</textarea>";
			}elseif($form[$i]['TYPE'] == "CHECKBOXARRAY"){
				if(isset($form[$i]['DATAVALUE'])){
					if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}
				}
				for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
					$html .=" <input type=\"CHECKBOX\" name='".$form[$i]['VALUE'][$j]['NAME']."' $class $ID $STYLE value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['VALUE'][$j]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'C').">".$form[$i]['VALUE'][$j]['LABEL']."&nbsp;&nbsp;&nbsp;<br>";
					if(!isset($form[$i]['SEPERATOR_COUNT'])){ $form[$i]['SEPERATOR_COUNT'] = 3;}
					if( $j%($form[$i]['SEPERATOR_COUNT']) == ($form[$i]['SEPERATOR_COUNT']-1)){
						//$html .="<br>";
					}
				}
			}elseif($form[$i]['TYPE'] == "RADIO"){
					if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}
				for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
					$html .=" <input type=RADIO name='".$form[$i]['NAME']."' $class $ID $STYLE value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'C').">".$form[$i]['VALUE'][$j]['LABEL']."&nbsp;&nbsp;&nbsp;";
					//$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
				}
				//$html .="<input type=radio name='".$form[$i]['NAME']."' $class $ID $STYLE value='".$form[$i]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'],'R').">";
			}elseif($form[$i]['TYPE'] == "CHECKBOXMULTI"){
				if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						$form[$i]['VALUE'] = $form[$i]['DATAVALUE']['SELECT_JSON'];
					}
				for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
					$html .="<div style=\"min-width:100px;float:left;\"><input type=CHECKBOX name='".$form[$i]['NAME']."[]' $class $ID $STYLE value='".$form[$i]['VALUE'][$j]['VALUE']."' ";
					
					if(is_array($_REQUEST[$form[$i]['NAME']])){
//						foreach($_REQUEST[$form[$i]['NAME']] as $key => $value){
							$html .= " ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'C')." ";
//						}
					}
					$html .= " > ".$form[$i]['VALUE'][$j]['LABEL']."&nbsp;&nbsp;&nbsp;</div>";
					//$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
				}//print_r($_REQUEST[$form[$i]['NAME']]); 
				$html .= "<br>";
				
				//$html .="<input type=radio name='".$form[$i]['NAME']."' $class $ID $STYLE value='".$form[$i]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'],'R').">";
			}elseif($form[$i]['TYPE'] == "CHECKBOX"){
				$html .="<input type=CHECKBOX name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".$form[$i]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'],'C').">";
			}elseif($form[$i]['TYPE'] == "SELECT"){
				$html .="<select name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE  $required>";
				if(isset($form[$i]['DATAVALUE'])){
					if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="\n<option value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S').">".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']."</option>";
						}
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="\n<option value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S').">".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']."</option>";
						}
					}
				}else if(is_array($form[$i]['VALUE'])){
					for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
						if( strpos($form[$i]['VALUE'][$j]['VALUE'],'\'')>0){
							$value2Match = substr($form[$i]['VALUE'][$j]['VALUE'],0,(strpos($form[$i]['VALUE'][$j]['VALUE'],"'")));
							$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$value2Match,'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
						}else{
							 
							$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['VALUE'][$j]['VALUE'],'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
						}
					}
				}
				$html .="</select>";
			}elseif($form[$i]['TYPE'] == "SELECTMULTI"){
				if(!isset($form[$i]['SIZE'])){
					$form[$i]['SIZE'] = 5;
				}
				$html .="<select name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' size=".$form[$i]['SIZE']." $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE multiple  $required>";
				if(isset($form[$i]['DATAVALUE'])){
					if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="\n<option value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S').">".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']."</option>";
						}
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="\n<option value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S').">".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']."</option>";
						}
					}
				}else if(is_array($form[$i]['VALUE'])){
					$val = $_REQUEST[$form[$i]['NAME']];
					$valueTocheck = explode(',',$val);
					for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
						if( strpos($form[$i]['VALUE'][$j]['VALUE'],'\'')>0){
							$value2Match = substr($form[$i]['VALUE'][$j]['VALUE'],0,(strpos($form[$i]['VALUE'][$j]['VALUE'],"'")));
							
							$valueChecked = '';
							
							foreach($valueTocheck as $k => $value){
								if($value==$form[$i]['VALUE'][$j]['VALUE']){
									$valueChecked = 'selected';
									break;
								}
							}
							//$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$value2Match,'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
							$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' $valueChecked>".$form[$i]['VALUE'][$j]['LABEL']."</option>";
						}else{
							$valueChecked = '';
							foreach($valueTocheck as $k => $value){
								if($value==$form[$i]['VALUE'][$j]['VALUE']){
									$valueChecked = 'selected';
									break;
								}
							}
							$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' $valueChecked >".$form[$i]['VALUE'][$j]['LABEL']."</option>";
						}
					}
				}
				$html .="</select>";
			}elseif($form[$i]['TYPE'] == "COLUMNFILTER"){
				$html .= <<<START
				
				<div class="mis_filters" style="float:right">
                  <ul>
                    <li class="filter_cols selected2 btn btn-info" >Select Column <img src="images/down.png" alt=""></li>
                  </ul>
                </div>
				<div class="filter_opts">
                  <div>
                    <h6>click on <b>Add</b> to add columns to the report</h6>
                    <ul class="columns_data">
START;
				if(isset($form[$i]['DATAVALUE'])){
					if(isset($form[$i]['DATAVALUE']['SELECT_JSON'])){
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="<li reldata='".$form[$i]['VALUE'][$j]['VALUE']."'><span><a href=\"javascript:void(0);\" rel='".$form[$i]['VALUE'][$j]['VALUE']."' >Add</a></span>\n<input type='checkbox' value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S') ."".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']." />";
						}
					}else if(isset($form[$i]['DATAVALUE']['SELECT_SRC']) && $form[$i]['DATAVALUE']['SELECT_SRC'] != ""){
						$form[$i]['DATAVALUE']['SELECT_JSON']=queryToOptions("SELECT ".$form[$i]['DATAVALUE']['SELECT_VALUE'].",".$form[$i]['DATAVALUE']['SELECT_LABEL']." from ".$form[$i]['DATAVALUE']['SELECT_SRC']." where ACTIVE = 'Y' and ".$form[$i]['DATAVALUE']['SELECT_CONDITION']." ",true);
						for($j=0;$j<sizeof($form[$i]['DATAVALUE']['SELECT_JSON']);$j++){
							$html.="\n<input type='checkbox' value='".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['VALUE'],'S') ."".$form[$i]['DATAVALUE']['SELECT_JSON'][$j]['LABEL']." />";
						}
					}
				}else if(is_array($form[$i]['VALUE'])){
					$val = $_REQUEST[$form[$i]['NAME']];
					$valueTocheck = explode(',',$val);
					for($j=0;$j<sizeof($form[$i]['VALUE']);$j++){
						if( strpos($form[$i]['VALUE'][$j]['VALUE'],'\'')>0){
							$value2Match = substr($form[$i]['VALUE'][$j]['VALUE'],0,(strpos($form[$i]['VALUE'][$j]['VALUE'],"'")));
							
							$valueChecked = '';
							
							foreach($valueTocheck as $k => $value){
								if($value==$form[$i]['VALUE'][$j]['VALUE']){
									$valueChecked = 'selected';
									break;
								}
							}
							//$html.="\n<option value='".$form[$i]['VALUE'][$j]['VALUE']."' ".getValueGPC($form[$i]['NAME'],$value2Match,'S').">".$form[$i]['VALUE'][$j]['LABEL']."</option>";
							$html.="<li reldata='".$form[$i]['VALUE'][$j]['VALUE']."'><span><a href=\"javascript:void(0);\" rel='".$form[$i]['VALUE'][$j]['VALUE']."' >Add</a></span>".$form[$i]['VALUE'][$j]['LABEL']."</li>\n<input type='checkbox' name=".$form[$i]['NAME']."[]  value='".$form[$i]['VALUE'][$j]['VALUE']."' id='_".$form[$i]['VALUE'][$j]['VALUE']."' />";
						}else{
							$valueChecked = '';
							foreach($valueTocheck as $k => $value){
								if($value==$form[$i]['VALUE'][$j]['VALUE']){
									$valueChecked = 'selected';
									break;
								}
							}
							$html.="<li reldata='".$form[$i]['VALUE'][$j]['VALUE']."' ><span><a href=\"javascript:void(0);\" rel='".$form[$i]['VALUE'][$j]['VALUE']."' >Add</a></span>".$form[$i]['VALUE'][$j]['LABEL']."</li>";
							
							//\n<input type='checkbox' name=".$form[$i]['NAME']."[] value='".$form[$i]['VALUE'][$j]['VALUE']."' id='_".$form[$i]['VALUE'][$j]['VALUE']."'  style='display:none'/>";
						}
					}
				}
				$html .="</ul>
                  </div><div>
                    <h6>Drag and drop to reorder</h6>
                    <ul class=\"uldrop\">
                    </ul>
                  </div>
				 <div class=\"filter_btn\">
                    <input type=\"checkbox\" class=\"chbx\" name=\"".$form[$i]['NAME']."_SAVE_REF\" id=\"filter_ref\" >
                    Save for future reference <span>
                    <input type=\"button\" class=\"fbtn\" value=\"Apply\" id=\"apply_filter\"  style=\"display: none;\">
                    <input type=\"button\" class=\"fbtn\" value=\"Save &amp; Apply\" id=\"save_filter\" style=\"display: none;\">
                    </span></div>
                <div>";
				$html.="<input type='hidden' name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' value='".getValueGPC($form[$i]['NAME'])."'>";
				$FormElementName= $form[$i]['NAME'];
			$html.=<<<START
			<script language="javascript" type="text/javascript">
$(document).ready(function(){
	
	$("#down_rep").hide();
	$(".toggle_down_rep").click(function(){
      $("#down_rep").toggle();
	  $(this).toggleClass("selected2");	  	  
    });	
	$("#close_rep").click(function(){
      $("#down_rep").hide();
	  $(".toggle_down_rep").toggleClass("selected2");	  	  
    });
	
	$(".filter_opts").hide();
	$(".filter_cols").click(function(){
      $(".filter_opts").slideToggle();
	  $(this).toggleClass("selected2");
	  $('html, body').animate({
    	scrollTop: $(".mis_filters").offset().top
		}, 800);  
    });	
	var tmpSelectColumn='';
	$(".columns_data li a").click(function(){	
		//alert('#_'+$(this).attr('rel'));
		
		//$("#_"+$(this).attr('rel')).prop( "checked", true );
		//alert($("#_"+$(this).attr('rel')).prop( "checked"));
		\$cloncol = $(this).parents("li").clone().appendTo("ul.uldrop");
		$(this).replaceWith("Added");//
		
		$('ul.uldrop li').each(function() { tmpSelectColumn += $(this).attr('reldata')+',';});
		tmpSelectColumn.substr(0,tmpSelectColumn.length - 1);
		$('#$FormElementName').attr('value',tmpSelectColumn.substr(0,tmpSelectColumn.length - 1));
		$("a", \$cloncol).text('remove').click(function(){
			$(this).parents("li").remove();
			//$('#_'+$(this).attr('rel')).prop( "checked", false );
			var tmpSelectColumn='';
		$('ul.uldrop li').each(function() { tmpSelectColumn += $(this).attr('reldata')+',';});
		tmpSelectColumn.substr(0,- 1);
		$('#$FormElementName').attr('value',tmpSelectColumn.substr(0,tmpSelectColumn.length - 1));
		});
	});
	$(".filter_opts").mouseout(function(){
		var tmpSelectColumn='';
		$("ul.uldrop li").each(function() {
		tmpSelectColumn += $ (this).attr("reldata")+",";});
		tmpSelectColumn.substr(0,- 1);
		$('#$FormElementName').attr('value',tmpSelectColumn.substr(0,tmpSelectColumn.length - 1));
	})
	$("#save_filter").hide("");
	$("#filter_ref").click(function(){
		if ($(this).attr("checked"))
		{
			$("#save_filter").show("");
			$("#apply_filter").hide("");
		}
		else {
			$("#save_filter").hide("");
			$("#apply_filter").show("");
		}
  	});
	$(".filter_btn span input").click(function(){
      $(".filter_opts").slideUp();
	  $(".filter_cols").toggleClass("selected2");		  
    });
	

});

var setSelector = ".uldrop";
$(function() {
	$(setSelector).sortable({
		axis: "y",
		cursor: "move",
		containment: "parent",
		update: function() { /*getOrder();*/ }
	});
});
</script>
START;

			}elseif($form[$i]['TYPE'] == "RESET"){
				$html .="<input type=RESET name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".$form[$i]['VALUE']."' >";
			}elseif($form[$i]['TYPE'] == "SUBMIT"){
				$html .="<input type=SUBMIT name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".$form[$i]['VALUE']."'";
				if(isset($form[$i]['ONCLICK_PARAMS'])){
					$html .=" onclick=\"javascript:showData('$PageName','".$_GET['checksum']."&".$form[$i]['ONCLICK_PARAMS']."&TYPE=$i');return false;\"";
					}
				$html .=">";
			}elseif($form[$i]['TYPE'] == "BUTTON"){
				$html .="<input type=button name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".$form[$i]['VALUE']."'";
				if(isset($form[$i]['ONCLICK_PARAMS'])){
					$html .=" onclick=\"javascript:showData('$PageName','".$_GET['checksum']."&".$form[$i]['ONCLICK_PARAMS']."&TYPE=$i');return false;\"";
					}
				$html .=">";
			}elseif($form[$i]['TYPE'] == "DATEPAIR"){
				if($form[$i]['CONDITION']['TYPE'] == "JQUERY" || true){
					$html .="<div class=\"input-daterange input-group\" id=\"".$form[$i]['NAME']."\" $class $ID $STYLE ><input type='text' $class  name='".$form[$i]['NAME']."_start' id='".$form[$i]['NAME']."_start' $changeEvent $READ_ONLY value='".getValueGPC($form[$i]['NAME'].'_start')."' > <span class=\"input-group-addon\">to</span><input type='text' $class  name='".$form[$i]['NAME']."_end' id='".$form[$i]['NAME']."_end' $changeEvent $READ_ONLY value='".getValueGPC($form[$i]['NAME'].'_end')."' ><script>var today = new Date(); var mm = today.getMonth(); var yyyy = today.getFullYear();if(mm<10){ mm='0'+mm} /* today = yyyy+'-'+mm+'-'; /**/ if(\$('#".$form[$i]['NAME']."').val()==''){\$('#".$form[$i]['NAME']."').val(today);}\$('#".$form[$i]['NAME']."').datepicker({format: \"yyyy-mm-dd\", daysOfWeekDisabled: \"".$form[$i]['DAYSDISABLED']."\",autoclose: true,todayHighlight: true,toggleActive: true});</script></div>";
				}elseif($form[$i]['CONDITION']['TYPE'] == "NEW"){
					
					//$html .="<input  type=text name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE >";
				}else{	
					$date3_default = $form[$i]['START_DATE'];
					$date4_default = $form[$i]['END_DATE'];
					$myCalendar = new tc_calendar($form[$i]['NAME']."_START", true, false);
					$myCalendar->setIcon("images/iconCalendar.gif");
	  				$myCalendar->setDate(date('d', strtotime($date3_default))
		            , date('m', strtotime($date3_default))
        		    , date('Y', strtotime($date3_default)));
//					  $myCalendar->setPath("cal");
					$myCalendar->setYearInterval(@$form[$i]['CONDITION']['YEAR'], 2020);
					$myCalendar->setDatePair($form[$i]['NAME']."_START", $form[$i]['NAME']."_END", $date4_default);
					$myCalendar->writeScript();
					$html .=	$myCalendar->ReturnHTMLDate; 
	  
					$myCalendar = new tc_calendar($form[$i]['NAME']."_END", true, false);
					$myCalendar->setIcon("images/iconCalendar.gif");
					$myCalendar->setDate(date('d', strtotime($date4_default))
        		   , date('m', strtotime($date4_default))
		           , date('Y', strtotime($date4_default)));
//				  $myCalendar->setPath("");
					$myCalendar->setYearInterval(@$form[$i]['CONDITION']['YEAR'], 2020);
					$myCalendar->setAlignment('left', 'bottom');
					$myCalendar->setDatePair($form[$i]['NAME']."_START", $form[$i]['NAME']."_END", $date3_default);
					//$myCalendar->setDatePair('date3', 'date4', $date3_default);
					$myCalendar->writeScript();
					$html .=	 $myCalendar->ReturnHTMLDate; 
					}
					
					
				}elseif($form[$i]['TYPE'] == "MULTIDATE"){
					if($form[$i]['CONDITION']['TYPE'] == "JQUERY"){
						if(!isset($form[$i]['AUTOCLOSE']) || $form[$i]['AUTOCLOSE'] != ''){
							$form[$i]['AUTOCLOSE'] = "false";
						}
					$html .="<input type='text' name='".$form[$i]['NAME']."' PlaceHolder='".$form[$i]['TITLE']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."'  datepicker=''><script>\$('#".$form[$i]['NAME']."').datepicker({format: \"yyyy-mm-dd\", daysOfWeekDisabled: \"".$form[$i]['DAYSDISABLED']."\", multidate: true,autoclose: ".$form[$i]['AUTOCLOSE'].",todayHighlight: true,toggleActive: true});</script>";
				}else{
					$html .="<input type=text name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' class=\"hasDatepicker\" $changeEvent onclick=\"$('#".$form[$i]['NAME']."').multiDatesPicker();\" $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."'>
					<script>$('#".$form[$i]['NAME']."').multiDatesPicker();</script>";
				}
				}
				elseif($form[$i]['TYPE'] == "DATE"){
				if($form[$i]['CONDITION']['TYPE'] == "JQUERY" || true){
					$html .="<input type='text' name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' PlaceHolder='".$form[$i]['TITLE']."' $tourEvent $changeEvent $READ_ONLY $class $ID $STYLE value='".getValueGPC($form[$i]['NAME'])."'  datepicker=''><script>try{\$('#".$form[$i]['NAME']."').datepicker({format: \"yyyy-mm-dd\", daysOfWeekDisabled: \"".$form[$i]['DAYSDISABLED']."\",autoclose: true,todayHighlight: true,toggleActive: true});}catch(err){}</script>";
				}elseif($form[$i]['CONDITION']['TYPE'] == "NEW"){
					if(isset($form[$i]['ONCHANGE'])){
						global  $_DATA;
						$_FORMDATA[$form[$i]['NAME']]['ONCHANGE'] = "onchange='".$form[$i]['ONCHANGE']."'";
					}
					$myCalendar = new tc_calendar($form[$i]['NAME']."", true, false);
	  				$myCalendar->setIcon("images/iconCalendar.gif");
	  				if(getValueGPC($form[$i]['NAME']) != ""){
//	  				print_r(getValueGPC($form[$i]['NAME']));
	  					//echo getValueGPC($form[$i]['NAME'])." ".$form[$i]['NAME'];
	  					$myCalendar->setDate((int)substr(getValueGPC($form[$i]['NAME']),8,2), (int)substr(getValueGPC($form[$i]['NAME']),5,2), (int)substr(getValueGPC($form[$i]['NAME']),0,4));
	  					//echo substr(getValueGPC($form[$i]['NAME']),8,2).substr(getValueGPC($form[$i]['NAME']),5,2). substr(getValueGPC($form[$i]['NAME']),0,4);
	  				}else{
		  				$myCalendar->setDate(date('d'), date('m'), date('Y'));
		  			}
	  				//$myCalendar->setPath("calendar/");
	 				$myCalendar->setYearInterval(@$form[$i]['CONDITION']['YEAR'], 2015);
	  				//$myCalendar->dateAllow('2008-05-13', '2015-03-01');
	  				$myCalendar->setDateFormat('j F Y');
	  				$myCalendar->setAlignment('left', 'bottom');
	  				//$myCalendar->setSpecificDate(array("2011-04-01", "2011-04-04", "2011-12-25"), 0, 'year');
	  				$myCalendar->writeScript();
	  				$html .=	 $myCalendar->ReturnHTMLDate; 

				if(isset($form[$i]['ONCHANGE'])){
						unset($_FORMDATA[$form[$i]['NAME']]['ONCHANGE']);
					}
				}else{
					$html .="<SELECT  name='DD_".$form[$i]['NAME']."' $class $ID $STYLE >";
					$html .="\n<option value=\"\">DAY</option>";
					for($ij=1;$ij<=31;$ij++){
						$html .="\n<option value=\"$ij\" ".getValueGPC("DD_".$form[$i]['NAME'],$ij,'S').">$ij</option>";
					}$html .="</SELECT>";
					$html .="&nbsp;<SELECT  name='MM_".$form[$i]['NAME']."' $class $ID $STYLE >";
					$html .="\n<option value=\"\">MONTH</option>";
					for($ij=1;$ij<=12;$ij++){
						$html .="\n<option value=\"$ij\" ".getValueGPC("MM_".$form[$i]['NAME'],$ij,'S').">$ij</option>";
					}$html .="</SELECT>";
					$html .="&nbsp;<SELECT  name='YY_".$form[$i]['NAME']."' $class $ID $STYLE >";
					$html .="\n<option value=\"\">YEAR</option>";
					$ij=2010;$j=$ij-100;
				
					IF($form[$i]['CONDITION'] == "CURRENT"){
						for($ij=$ij;$ij>$j;$ij--){
							$html .="\n<option value=\"$ij\" ".getValueGPC("YY_".$form[$i]['NAME'],$ij,'S').">$ij</option>";
						}
					}ELSE{
					for($ij=$ij-18;$ij>$j;$ij--){
						$html .="\n<option value=\"$ij\" ".getValueGPC("YY_".$form[$i]['NAME'],$ij,'S').">$ij</option>";
					}}
					$html .="</SELECT>";
				}
				
			}elseif($form[$i]['TYPE'] == "INDIA_STATE_LIST"){
				//global $STATE_SELECT_LIST;
				include 'formLists.php';
				$html .="<SELECT  name='SL_".$form[$i]['NAME']."' $class $ID $STYLE >";
				if(getValueGPC('SL_'.$form[$i]['NAME'])!="")
				$html.="\n<option value=\"".getValueGPC('SL_'.$form[$i]['NAME'])."\">".getValueGPC('SL_'.$form[$i]['NAME'])."</option>";
				$html .=$INDIA_STATE_SELECT_LIST;
				$html .="</SELECT>";
			}elseif($form[$i]['TYPE'] == "NOMINEE"){
				$html .="<input type=text name='NN_".$form[$i]['NAME']."' $class $ID $STYLE value='".getValueGPC("NN_".$form[$i]['NAME'])."'>";
				$html .="&nbsp;Relation <input type=text name='NR_".$form[$i]['NAME']."' $class $ID $STYLE value='".getValueGPC("NR_".$form[$i]['NAME'])."'>";
				$html .="&nbsp;&nbsp;Age <input type=text name='NA_".$form[$i]['NAME']."' $class $ID $STYLE value='".getValueGPC("NA_".$form[$i]['NAME'])."'>";
			}elseif($form[$i]['TYPE'] == "SUBMIT_RESET"){
				$html .="<br><br><input type=submit name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $changeEvent $clientSideValidation $READ_ONLY $class $ID $STYLE value='".$form[$i]['VALUE']."' >";
				$html .="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=reset value=RESET $class $ID $STYLE >";
			}elseif($form[$i]['TYPE'] == "HTMLEDITOR"){
				//$AJAXProcess = false;$FE_Name = $form[$i]['NAME'];
				//$htmlFormTinyMCEProcess .= "document.getElementById('$FE_Name').InnerHTML=tinyMCE.get('$FE_Name').getContent();alert(document.getElementById('$FE_Name').InnerHTML)";
				//$TinyMce = <<<TMCE
				//<script type="text/javascript">tinyMCE.init({ mode : "exact",elements : '$FE_Name',theme : "advanced",plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",theme_advanced_toolbar_location : "top",theme_advanced_toolbar_align : "left",theme_advanced_statusbar_location : "bottom",theme_advanced_resizing : true,width: "90%",height: "400"});</script>
//TMCE;/**/
				$html .="<textarea name='".$form[$i]['NAME']."' id='".$form[$i]['NAME']."' $tourEvent $changeEvent $clientSideValidation $READ_ONLY $class id='".$form[$i]['NAME']."' style=\"width:100%\">".getValueGPC($form[$i]['NAME'])."</textarea>";
				$_FORMIDNAME = $form[$i]['NAME'];
				$FormEgninePath = $Config->FormEnginePath;
				$html .=<<<SCRIPT
		<script>
		$('#$_FORMIDNAME').redactor({
		lang: 'en',
		cleanOnPaste: false,
		linkTooltip: true,
		paragraphize: false,
		imageUpload: '$FormEgninePath/redactor/inc/editor_images.php',
		imageManagerJson: '$FormEgninePath/redactor/inc/data_json.php',
		fileUpload: '$FormEgninePath/redactor/inc/editor_files.php',
		replaceDivs: true,
		autoresize: false,
		minHeight: 400,
		buttonSource: false,
		plugins: ['imagemanager', 'video']
});</script>
SCRIPT;
			}elseif($form[$i]['TYPE'] == "IMAGEUPLOAD" ){
				$extenstion = "";
				if(isset($form[$i]['ALLOW_EXTENSION'])){
					$extenstion = ", allowExt:[".$form[$i]['ALLOW_EXTENSION']."]";
				}
				$html .="<input type=\"hidden\" name=\"".$form[$i]['NAME']."\" id=\"".$form[$i]['NAME']."\" value='".getValueGPC($form[$i]['NAME'])."' $tourEvent >";
				if(getValueGPC($form[$i]['NAME']) != ""){
					$html .="<div id=\"".$form[$i]['NAME']."_ulp\"><img src=\"".$Config->FormEnginePath.'/'.$form[$i]['REMOTE_PATH']."/".getValueGPC($form[$i]['NAME'])."\" height=\"100px\"></div>";					
				}
				//else
				{
					$html .="<div id=\"".$form[$i]['NAME']."_ul\"></div>";
				}
				if(!isset($form[$i]['REMOTE_PATH']) || trim($form[$i]['REMOTE_PATH']) == ''){$form[$i]['REMOTE_PATH'] ='usercontent/';}
				if(getValueGPC('AJAX') != ""){
				$html .="<script type=\"text/javascript\">".
				"jQuery( function($) { 
						\$('#".$form[$i]['NAME']."_ul').ajaxupload(
								{url:'".$Config->FormEnginePath."/upload.php',remotePath:'".$form[$i]['REMOTE_PATH']."' $extenstion ,autoStart:true,
								finish:function(files, filesObj){
								      //  alert('All files has been uploaded: '+ info+ filesObj);
								    },
								    success:function(file,info){
								    	document.getElementById('".$form[$i]['NAME']."').value = file;
//								    	$(\"#'".$form[$i]['NAME']."_ul'\").
//								    	_v = document.getElementById('".$form[$i]['NAME']."_ul');
//								    	_v.getElementById('addButton_upldr').style.display = \"none\";
//								        console.log('File '+ file + info + ' uploaded correctly');
								    },
								    beforeUpload: function(filename, fileobj){
								        if(filename.length>250){
								        	alert('FileName Must be less than 50 Char');
								            return false; //file will not be uploaded
								        }
								        else
								        {
								            return true; //file will be uploaded
								        }
								    },
								    error:function(txt, obj){
								         alert('An error occour '+ txt +'#'+ obj);
								    } 
								});
							}
						);".
						
				"</script>";}
				else if(getValueGPC('AJAX') == ""){
				$html .="<script type=\"text/javascript\">".
				"window.onload = photoLoad = function() { jQuery( function($) { \n
						$('#".$form[$i]['NAME']."_ul').ajaxupload(
								{url:'".$Config->FormEnginePath."/upload.php',remotePath:'".$form[$i]['REMOTE_PATH']."' $extenstion ,autoStart:true,
								finish:function(files, filesObj){
								      //  alert('All files has been uploaded: '+ info+ filesObj);
								    },
								    success:function(file,info){
								    	document.getElementById('".$form[$i]['NAME']."').value = file;
//								    	$(\"#'".$form[$i]['NAME']."_ul'\").
//								    	_v = document.getElementById('".$form[$i]['NAME']."_ul');
//								    	_v.getElementById('addButton_upldr').style.display = \"none\";
//								        console.log('File '+ file + info + ' uploaded correctly');
								    },
								    beforeUpload: function(filename, fileobj){
								        if(filename.length>250){
								        	alert('FileName Must be less than 50 Char');
								            return false; //file will not be uploaded
								        }
								        else
								        {
								            return true; //file will be uploaded
								        }
								    },
								    error:function(txt, obj){
								         alert('An error occour '+ txt +'#'+ obj.info);
								  } 
								});
				 			 }); \n
							};".
				"</script>";
				}
			}elseif($form[$i]['TYPE'] == "IMAGEUPLOADMULTI" ){
				$extenstion = "";
				if(isset($form[$i]['ALLOW_EXTENSION'])){
					$extenstion = ", allowExt:[".$form[$i]['ALLOW_EXTENSION']."]";
				}
				$html .="<input type=\"hidden\" name=\"".$form[$i]['NAME']."\" id=\"".$form[$i]['NAME']."\" value='".getValueGPC($form[$i]['NAME'])."' $tourEvent>";
				if(getValueGPC($form[$i]['NAME']) != ""){
					$resultArray = explode(",",getValueGPC($form[$i]['NAME']));
					foreach($resultArray as $key => $value){
						if(trim($value)!="")
						$html .="<div id=\"".$form[$i]['NAME']."_ulp\"><img src=\"".$form[$i]['REMOTE_PATH']."/".$value."\" height=\"100px\"></div>";					
					}
				}
				//else 
				{
					$html .="<div id=\"".$form[$i]['NAME']."_ul\"></div>";
				}
				if(getValueGPC('AJAX') != ""){
				$html .="<script type=\"text/javascript\">".
				"jQuery( function($) { 
						\$('#".$form[$i]['NAME']."_ul').ajaxupload(
								{url:'".$Config->FormEnginePath."/upload.php',remotePath:'".$form[$i]['REMOTE_PATH']."' $extenstion ,autoStart:true,
								finish:function(files, filesObj){
								      //  alert('All files has been uploaded: '+ info+ filesObj);
								    },
								    success:function(file,info){
								    	document.getElementById('".$form[$i]['NAME']."').value = document.getElementById('".$form[$i]['NAME']."').value+','+file;;
//								    	$(\"#'".$form[$i]['NAME']."_ul'\").
//								    	_v = document.getElementById('".$form[$i]['NAME']."_ul');
//								    	_v.getElementById('addButton_upldr').style.display = \"none\";
//								        console.log('File '+ file + info + ' uploaded correctly');
								    },
								    beforeUpload: function(filename, fileobj){
								        if(filename.length>250){
								        	alert('FileName Must be less than 50 Char');
								            return false; //file will not be uploaded
								        }
								        else
								        {
								            return true; //file will be uploaded
								        }
								    },
								    error:function(txt, obj){
								         alert('An error occour '+ txt +'#'+ obj);
								    } 
								});
							}
						);".
						
				"</script>";}
				else if(getValueGPC('AJAX') == ""){
				$html .="<script type=\"text/javascript\">".
				"window.onload = function() { jQuery( function($) { \n
						$('#".$form[$i]['NAME']."_ul').ajaxupload(
								{url:'".$Config->FormEnginePath."/upload.php',remotePath:'".$form[$i]['REMOTE_PATH']."' $extenstion ,autoStart:true,
								finish:function(files, filesObj){
								      //  alert('All files has been uploaded: '+ info+ filesObj);
								    },
								    success:function(file,info){
								    	document.getElementById('".$form[$i]['NAME']."').value = document.getElementById('".$form[$i]['NAME']."').value+','+file;
//								    	$(\"#'".$form[$i]['NAME']."_ul'\").
//								    	_v = document.getElementById('".$form[$i]['NAME']."_ul');
//								    	_v.getElementById('addButton_upldr').style.display = \"none\";
//								        console.log('File '+ file + info + ' uploaded correctly');
								    },
								    beforeUpload: function(filename, fileobj){
								        if(filename.length>250){
								        	alert('FileName Must be less than 50 Char');
								            return false; //file will not be uploaded
								        }
								        else
								        {
								            return true; //file will be uploaded
								        }
								    },
								    error:function(txt, obj){
								         alert('An error occour '+ txt +'#'+ obj.info);
								  } 
								});
				 			 }); \n
							};".
						
				"</script>";
				}
			}
			
			if( isset($_DATA['Err_'.$form[$i]['NAME']])){
				//$html .="$astrixMand<br><font color=red>".$_DATA['Err_'.$form[$i]['NAME']]."</font>";
				$html .="$astrixMand<br>".'<div class="error" style="display:block;" id="ERROR_'.$form[$i]['NAME'].'">
                                                <p class="error-block"><span>'.$_DATA['Err_'.$form[$i]['NAME']].'</span></p>
                                            </div>';
			}else{$html .="$astrixMand".'<div class="error" style="display:none;" id="ERROR_'.$form[$i]['NAME'].'">
                                                <p class="error-block"><span></span></p>
                                            </div>';;}//*/
			
			if(@$form[$i+1]['TYPE'] == "HTMLEDITOR"){
				$html.="\n\t<$TableColEndTag>\n$TableRowEndTag";
			}elseif($form[$i]['TYPE'] != "HIDDEN"){
				$html.="\n\t<$TableColEndTag>\n$TableRowEnd";}
			
		}
		return $html;
	}
?>