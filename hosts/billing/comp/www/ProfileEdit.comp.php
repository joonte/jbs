<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ProfileID    = (integer) @$Args['ProfileID'];
$TemplateID   =  (string) @$Args['TemplateID'];
$TemplatesIDs =  (string) @$Args['TemplatesIDs'];
$Simple       =  (string) @$Args['Simple'];
$Window       =  (string) @$Args['Window'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($ProfileID){
	#-------------------------------------------------------------------------------
	$Profile = DB_Select('Profiles',Array('ID','UserID','TemplateID','Attribs','Format'),Array('UNIQ','ID'=>$ProfileID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Profile)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$TemplateID = $Profile['TemplateID'];
		#-------------------------------------------------------------------------------
		$IsPermission = Permission_Check('ProfilesEdit',(integer)$__USER['ID'],(integer)$Profile['UserID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsPermission)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'false':
			return ERROR | @Trigger_Error(700);
		case 'true':
			# No more...
			break 2;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ProfileEditForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
if($Window){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'Window','type'=>'hidden','value'=>$Window));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$TemplateID){
	#-------------------------------------------------------------------------------
	$DOM->AddText('Title','Новый профиль');
	#-------------------------------------------------------------------------------
	$Config = Config();
	#-------------------------------------------------------------------------------
	$Templates = $Config['Profiles']['Templates'];
	#-------------------------------------------------------------------------------
	$Options = Array();
	#-------------------------------------------------------------------------------
	$TemplatesIDs = ($TemplatesIDs?Explode(',',$TemplatesIDs):Array());
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Templates) as $TemplateID){
		#-------------------------------------------------------------------------------
		if(Count($TemplatesIDs) && !In_Array($TemplateID,$TemplatesIDs))
			continue;
		#-------------------------------------------------------------------------------
		$Template = $Templates[$TemplateID];
		#-------------------------------------------------------------------------------
		if($Template['IsActive'])
			$Options[$TemplateID] = $Templates[$TemplateID]['Name'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(!Count($Options))
		return new gException('TEMPLATES_NOT_DEFINED','Шаблоны не определены');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'TemplateID'),$Options);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table = Array(Array('Шаблон',$Comp));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('onclick'=>"ShowWindow('/ProfileEdit',FormGet(form));",'type'=>'button','value'=>'Продолжить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$DOM->AddText('Title',$Config['Profiles']['Templates'][$TemplateID]['Name']);
	#-------------------------------------------------------------------------------
	$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ProfileEdit.js}'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Head',$Script);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'TemplateID','type'=>'hidden','value'=>$TemplateID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	if($ProfileID){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'ProfileID','type'=>'hidden','value'=>$Profile['ID']));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Template = System_XML(SPrintF('profiles/%s.xml',$TemplateID));
	if(Is_Error($Template))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($Simple){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'Simple','type'=>'hidden','value'=>$Simple));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
		$Simple = @JSON_Decode(Base64_Decode($Simple),TRUE);
		if(!$Simple)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Simple = Array();
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Attribs = $Template['Attribs'];
	#-------------------------------------------------------------------------------
	$Replace = Array_ToLine($__USER,'%');
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Attribs) as $AttribID){
		#-------------------------------------------------------------------------------
		$Attrib = $Attribs[$AttribID];
		#-------------------------------------------------------------------------------
		if(Count($Simple)){
			#-------------------------------------------------------------------------------
			if(!IsSet($Simple[$AttribID]))
				continue;
			#-------------------------------------------------------------------------------
			$Attrib['IsDuty'] = $Simple[$AttribID];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(IsSet($Attrib['Title']))
			$Table[] = $Attrib['Title'];
		#-------------------------------------------------------------------------------
		if($ProfileID){
			#-------------------------------------------------------------------------------
			$Value = (string)@$Profile['Attribs'][$AttribID];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Value = $Attrib['Value'];
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Replace) as $Key)
				$Value = Str_Replace($Key,$Replace[$Key],$Value);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Params = &$Attrib['Attribs'];
		#-------------------------------------------------------------------------------
		$Params['name'] = $AttribID;
		#-------------------------------------------------------------------------------
		if($Attrib['IsDuty'])
			$Params['class'] = 'Duty';
		#-------------------------------------------------------------------------------
		switch($Attrib['Type']){
		case 'Input':
			#-------------------------------------------------------------------------------
			$Params['value'] = $Value;
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',$Params);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(101);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'TextArea':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/TextArea',$Params,$Value);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(101);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'Select':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Select',$Params,$Attrib['Options'],$Value);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(101);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$NoBody = new Tag('NOBODY',new Tag('SPAN',$Attrib['Comment']));
		#-------------------------------------------------------------------------------
		$NoBody->AddChild(new Tag('BR'));
		#-------------------------------------------------------------------------------
		if(IsSet($Attrib['Example']))
			$NoBody->AddChild(new Tag('SPAN',Array('class'=>'Comment'),SPrintF('Например: %s',$Attrib['Example'])));
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/www/ProfileEdit]: Attrib = %s',print_r($Attrib,true)));
		if(!IsSet($Attrib['NotActive']))
			$Table[] = Array($NoBody,$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$Simple){
		#-------------------------------------------------------------------------------
		$Table[] = 'Подтверждение введенных данных';
		#-------------------------------------------------------------------------------
		$Files = GetUploadedFilesInfo('Profiles',$ProfileID);
		#-------------------------------------------------------------------------------
		$Out = '';
		#-------------------------------------------------------------------------------
		foreach($Files as $File){
			#-------------------------------------------------------------------------------
			// если разрешено удалять файл или это админ
			if($Config['Interface']['User']['Files']['Profiles']['AllowDelete'] || $__USER['IsAdmin']){
				#-------------------------------------------------------------------------------
				$Delete = SPrintF('title="Удалить файл" style="cursor:pointer;font-size:11px;text-decoration:underline;" onclick="JavaScript:ShowConfirm(\'Вы подтверждаете удаление файла?\',\'AjaxCall(\\\'/API/FileDelete\\\',{FileID:%u},\\\'Удаление файла\\\',\\\'UploadHideFile(\\\\\\\'file_%s\\\\\\\');\\\');\');"',$File['ID'],$File['ID']);
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$Delete = 'style="font-size:11px;"';
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$NoBR = SPrintF('<NOBR id="file_%s" %s>%s%s/%01.2fkB</NOBR>',$File['ID'],$Delete,($Out)?'<BR />':'',Mb_SubStr($File['Name'],0,16),$File['Size']/1024);
			#-------------------------------------------------------------------------------
			$Out = SPrintF('%s%s',$Out,$NoBR);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Upload','Document',SizeOf($Files)?$Out:'-',$Config['Interface']['User']['Files']['Profiles']['MaxFiles']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Копия документа подтверждающего достоверность данных',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$__USER['IsAdmin']){
		#-------------------------------------------------------------------------------
		$Table[] = 'Обработка персональных данных №152-ФЗ "О персональных данных"';
		#-------------------------------------------------------------------------------
		$Agree = 'Я подтверждаю своё согласие на передачу информации в электронной форме (в том числе персональных данных) по открытым каналам связи сети Интернет.';
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'Agree','type'=>'checkbox','value'=>'yes','prompt'=>$Agree));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array($Agree,$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Agree = 'Я разрешаю передачу своих персональных данных третьим лицам, для выполнения заказанных мною услуг (регистрация доменов и т.п.)';
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'AgreeHandle','type'=>'checkbox','value'=>'yes','prompt'=>$Agree));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array($Agree,$Comp);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Table[] = 'Проверка корректности введённых данных';
		#-------------------------------------------------------------------------------
		$Prompt = 'При установке галочки проверяется правильность заполнения полей с данными. Если галочку не устанавливать - проверка не производится, можно сохранить неполный профиль, но, также, пропускаются ошибки заполнения.';
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'CheckFields','type'=>'checkbox','checked'=>'yes','value'=>'yes','prompt'=>$Prompt));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Проверить данные',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'ProfileEdit();','value'=>($ProfileID?'Сохранить':'Зарегистрировать')));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table,Array('style'=>'width:500px;'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
