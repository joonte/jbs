<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','UserID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

if($UserID){
	#-------------------------------------------------------------------------------
	$Users = DB_Select('Users',Array('ID','Email','Params'),Array('ID'=>$UserID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Users)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Users = DB_Select('Users',Array('ID','Email','Params'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Users)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Template = System_XML('xml/Params/Users.xml');
if(Is_Error($Template))
	return new gException('ERROR_TEMPLATE_LOAD','Ошибка загрузки шаблона');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Users as $User){
	#-------------------------------------------------------------------------------
	$Attribs = @$User['Params']['Settings'];
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Template['Settings']) as $AttribID)
		if(!IsSet($Attribs[$AttribID]))
			$Attribs[$AttribID] = $Template['Settings'][$AttribID]['Value'];
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Attribs) as $AttribID)
		if(!IsSet($Template['Settings'][$AttribID]))
			UnSet($Attribs[$AttribID]);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Params = Array();
	#-------------------------------------------------------------------------------
	$Params['Settings'] = $Attribs;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($User['Params']['IsAutoRegistered']) && $User['Params']['IsAutoRegistered']){
		#-------------------------------------------------------------------------------
		$Params['IsAutoRegistered'] = TRUE;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Params['IsAutoRegistered'] = FALSE;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Users',Array('Email'=>StrToLower($User['Email']),'Params'=>$Params),Array('ID'=>$User['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// JBS-1125: проверяем корректность настройки первичного адреса (логина) у клиентов
	$Where = SPrintF("`UserID` = %u AND `Address` = '%s' AND `MethodID` = 'Email' AND `IsPrimary` = 'yes'",$User['ID'],$User['Email']);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Contacts',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count){
		#-------------------------------------------------------------------------------
		// чё-то не так. выбираем все почтовые адреса клиента, разбираемся
		$Contacts = DB_Select('Contacts',Array('ID','MethodID','Address'),Array('Where'=>Array(SPrintF('`UserID` = %u',$User['ID']),'`IsHidden` = "no"')));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Contacts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			// у юзера нет адресов. вообще... надо админу разбираться
			$Event = Array('UserID'=>100,'PriorityID'=>'Billing','IsReaded'=>FALSE,'Text'=>SPrintF('Обнаружен пользователь (%s/%s) без контактных адресов',$User['ID'],$User['Email']));
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			// почтовые адреса есть. перебираем, проверяем есть ли адрес соответствующий полю $User['Email']
			$ContactID = FALSE;
			#-------------------------------------------------------------------------------
			foreach($Contacts as $Contact)
				if($Contact['Address'] == $User['Email'])
					if($Contact['MethodID'] == 'Email')
						$ContactID = $Contact['ID'];
			#-------------------------------------------------------------------------------
			// если адрес найден, принудительно проставляем ему необходимые параметры
			if($ContactID){
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('Contacts',Array('IsPrimary'=>TRUE,'IsActive'=>TRUE),Array('ID'=>$ContactID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				// адрес не найден в таблице контактов. снова к админу
				$Event = Array('UserID'=>100,'PriorityID'=>'Billing','IsReaded'=>FALSE,'Text'=>SPrintF('Обнаружен пользователь (%s/%s) без контактного почтового адреса',$User['ID'],$User['Email']));
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
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
	// другой вариант  - более одного первичного адреса
	$Where = SPrintF("`UserID` = %u AND `IsPrimary` = 'yes'",$User['ID']);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Contacts',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count > 1){
		#-------------------------------------------------------------------------------
		// сбрасываем все первичные адреса на обычные, перебираем контакты
		$IsUpdate = DB_Update('Contacts',Array('IsPrimary'=>FALSE),Array('Where'=>SPrintF('`UserID` = %u',$User['ID'])));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Contacts = DB_Select('Contacts',Array('ID','Address'),Array('Where'=>SPrintF('`UserID` = %u AND `MethodID` = "Email" AND `IsHidden` = "no"',$User['ID'])));
		#-------------------------------------------------------------------------------
                switch(ValueOf($Contacts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			// адрес не найден в таблице контактов. снова к админу
			$Event = Array('UserID'=>100,'PriorityID'=>'Billing','IsReaded'=>FALSE,'Text'=>SPrintF('Обнаружен пользователь (%s/%s) без контактных почтовых адресов, но более чем с одним первичным адресом',$User['ID'],$User['Email']));
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			// перебираем адреса, проверяем что такой есть
			$ContactID = FALSE;
			#-------------------------------------------------------------------------------
			foreach($Contacts as $Contact)
				if($Contact['Address'] == $User['Email'])
					$ContactID = $Contact['ID'];
			#-------------------------------------------------------------------------------
			// если адрес найден, принудительно проставляем ему необходимые параметры
			if($ContactID){
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('Contacts',Array('IsPrimary'=>TRUE,'IsActive'=>TRUE),Array('ID'=>$ContactID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				// адрес не найден в таблице контактов. снова к админу
				$Event = Array('UserID'=>100,'PriorityID'=>'Billing','IsReaded'=>FALSE,'Text'=>SPrintF('Обнаружен пользователь (%s/%s) без контактного почтового адреса, но более чем с одним первичным адресом',$User['ID'],$User['Email']));
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(SizeOf($Users) > 1){
	#-------------------------------------------------------------------------------
	$Event = Array('UserID'=>100,'PriorityID'=>'Billing','Text'=>SPrintF('Успешно восстановлено %u пользователей',SizeOf($Users)));
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = SPrintF('Recovered: %u users',SizeOf($Users));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
