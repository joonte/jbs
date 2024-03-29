<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ServerID	= (integer) @$Args['ServerID'];
$TemplateID	=  (string) @$Args['TemplateID'];
$Window		=  (string) @$Args['Window'];
$ServersGroupID	= (integer) @$Args['ServersGroupID'];
$IsActive	= (boolean) @$Args['IsActive'];
$IsDefault	= (boolean) @$Args['IsDefault'];
$Protocol	=  (string) @$Args['Protocol'];
$Address	=  (string) @$Args['Address'];
$Port		= (integer) @$Args['Port'];
$Login		=  (string) @$Args['Login'];
$Password	=  (string) @$Args['Password'];
$Monitoring	=  (string) @$Args['Monitoring'];
$AdminNotice	=  (string) @$Args['AdminNotice'];
$SortID		= (integer) @$Args['SortID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Protocol)
	return new gException('NO_SERVER_PROTOCOL','Не указан протокол подключения к серверу');
#-------------------------------------------------------------------------------
if(!$Address)
	return new gException('NO_SERVER_ADDRESS','Не указан адрес сервера');
#-------------------------------------------------------------------------------
if(!$Port)
	return new gException('NO_DESTINATION_PORT','Не указан порт сервера');
#-------------------------------------------------------------------------------
if(!$Login)
	return new gException('NO_SERVER_LOGIN','Не указан логин для входа на сервер');
#-------------------------------------------------------------------------------
if(!$Password)
	return new gException('NO_SERVER_PASSWORD','Не указан пароль для входа на сервер');
#-------------------------------------------------------------------------------
if(!$IsActive && $IsDefault)
	return new gException('DEFAULT_INACTIVE_SERVER','Неактивный сервер не может быть сервером по-умолчанию');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UServer = Array(
			'TemplateID'	=> $TemplateID,
			'ServersGroupID'=> ($ServersGroupID)?$ServersGroupID:NULL,
			'IsActive'	=> $IsActive,
			'IsDefault'	=> $IsDefault,
			'Protocol'	=> $Protocol,
			'Address'	=> $Address,
			'Port'		=> $Port,
			'Login'		=> $Login,
			'Monitoring'	=> $Monitoring,
			'AdminNotice'	=> $AdminNotice,
			'SortID'	=> $SortID
		);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Password != 'Default')
	$UServer['Password'] = $Password;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Template = System_XML(SPrintF('servers/%s.xml',$TemplateID));
if(Is_Error($Template))
	return new gException('ERROR_TEMPLATE_LOAD','Ошибка загрузки шаблона');
#-------------------------------------------------------------------------------
$Errors = $Attribs = Array();
#-------------------------------------------------------------------------------
if(IsSet($Template['Attribs'])){
	#-------------------------------------------------------------------------------
	$Params = $Template['Attribs'];
	#-------------------------------------------------------------------------------
	$Regulars = Regulars();
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Params) as $AttribID){
		#-------------------------------------------------------------------------------
		$Attrib = $Params[$AttribID];
		#-------------------------------------------------------------------------------
		$Value = (IsSet($Args[$AttribID])?$Args[$AttribID]:$Params[$AttribID]['Value']);
		#-------------------------------------------------------------------------------
		# костыль для чекбоксов
		if(IsSet($Attrib['Attribs']['type']) && $Attrib['Attribs']['type'] == 'checkbox')
			$Value = (boolean) @$Args[$AttribID];
		#-------------------------------------------------------------------------------
		$Attribs[$AttribID] = $Value;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		switch($Attrib['Type']){
		case 'Input':
			# No more...
		case 'Hidden':
			# No more...
		case 'TextArea':
			#-------------------------------------------------------------------------------
			if($Value){
				#-------------------------------------------------------------------------------
				$Check = $Attrib['Check'];
				#-------------------------------------------------------------------------------
				if(IsSet($Regulars[$Check]))
					$Check = $Regulars[$Check];
				#-------------------------------------------------------------------------------
				if(!Preg_Match($Check,$Value)){
					#-------------------------------------------------------------------------------
					$Errors[] = $AttribID;
					#-------------------------------------------------------------------------------
					//Debug(SPrintF('[comp/www/Administrator/API/ServerEdit]: Check = %s; Value = %s',$Check,$Value));
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				if($Attrib['IsDuty'])
					$Errors[] = $AttribID;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'Select':
			#-------------------------------------------------------------------------------
			if(!IsSet($Attrib['Options'][$Value]))
				$Errors[] = $AttribID;
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(100);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($Errors)){
	#-------------------------------------------------------------------------------
	$Attribs = $Template['Attribs'];
	#-------------------------------------------------------------------------------
	$Parent = NULL;
	#-------------------------------------------------------------------------------
	$Errors = Array_Reverse($Errors);
	#-------------------------------------------------------------------------------
	foreach($Errors as $AttribID){
		#-------------------------------------------------------------------------------
		$Attrib = $Attribs[$AttribID];
		#-------------------------------------------------------------------------------
		$Exception = new gException(StrToUpper($AttribID),$Attrib['Comment'],$Parent);
		#-------------------------------------------------------------------------------
		$Parent = $Exception;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($Exception))
		return new gException('FIELDS_WRONG_FILLED','Неверно заполнены поля',$Exception);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Attribs,'%');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем что все сервера в группе имеют одну систему управления
if($ServersGroupID && IsSet($Attribs['SystemID'])){
	#-------------------------------------------------------------------------------
	// группа серверов задана
	$Servers = DB_Select('Servers',Array('*'),Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroupID),'SortOn'=>'SortID'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Servers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# проверки не требуется
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		// првоеряем что все панели управления одинаковые
		foreach($Servers as $Server)
			if($Attribs['SystemID'] != $Server['Params']['SystemID'] && $TemplateID != 'Domain')
				return new gException('DIFFERENT_CONTROL_PANEL',SPrintF('В группе уже используются сервера с другой панелью управления %s: %s',$Server['Address'],$Server['Params']['SystemID']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем, что не меняют систему управления у существующего сервера
if($ServerID && IsSet($Attribs['SystemID'])){
	#-------------------------------------------------------------------------------
	$Server = DB_Select('Servers',Array('*'),Array('UNIQ','ID'=>$ServerID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Server)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		if($Attribs['SystemID'] != $Server['Params']['SystemID'])
			return new gException('CHANGE_CONTROL_PANEL',SPrintF('Нельзя менять панель управления (было:%s), улетят все настройки тарифных планов. При необходимости, сделайте это чреез базу, сохранив бэкап',$Server['Params']['SystemID']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------

	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-----------------------------TRANSACTION---------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ServerEdit'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$UServer['Params'] = $Attribs;
#-------------------------------------------------------------------------------
$Answer = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($IsDefault){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Servers',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroupID)));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($ServersGroupID){
		#-------------------------------------------------------------------------------
		$Count = DB_Count('Servers',Array('Where'=>SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$ServersGroupID)));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Count)
			$UServer['IsDefault'] = TRUE;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServerID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Servers',$UServer,Array('ID'=>$ServerID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ServerID = DB_Insert('Servers',$UServer);
	if(Is_Error($ServerID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Answer['ServerID'] = $ServerID;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Window){
	#-------------------------------------------------------------------------------
	$Window = JSON_Decode(Base64_Decode($Window),TRUE);
	#-------------------------------------------------------------------------------
	$Window['Args']['ServerID'] = $ServerID;
	#-------------------------------------------------------------------------------
	$Answer = Array('Status'=>'Window','Window'=>$Window);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Answer;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
