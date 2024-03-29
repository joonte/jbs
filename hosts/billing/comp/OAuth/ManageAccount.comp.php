<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('OAuthName','Address','ExtUserID','Name');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Address = StrToLower($Address);
#-------------------------------------------------------------------------------
// тексты сообщений
$Messages = Messages();
#-------------------------------------------------------------------------------
// конфигурация системы авторизации
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['OAuth'][$OAuthName];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// надо всё это в транзакцию завернуть, чтобы адрес не попортило
if(Is_Error(DB_Transaction($TransactionID = UniqID('OauthManageAccount'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// возможны варианты: добавляет авторизацию, тогда $GLOBALS['__USER']['ID'] задана, или же входит/регистрируется
if(IsSet($GLOBALS['__USER']['ID'])){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/OAuth/ManageAccount]: IsSet: GLOBALS[__USER][ID] = %s',$GLOBALS['__USER']['ID']));
	#-------------------------------------------------------------------------------
	// восстанавливаем юзера, были эксцессы...
	$Comp = Comp_Load('Tasks/RecoveryUsers',NULL,$GLOBALS['__USER']['ID']);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если это админ, вываливаемся с ошибкой
	if($GLOBALS['__USER']['IsAdmin'] && !$Settings['AllowForAdmin'])
		return new gException('NOT_ALLOWED_FOR_ADMINs',$Messages['Errors']['OAuth']['OAuthAllowForAdmin']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// проверяем что адрес не является первичным у какого-то другого юзера
	$Where = Array(SPrintF('`Address` = "%s"',$Address),SPrintF('`UserID` != %u',$GLOBALS['__USER']['ID']),'`MethodID` = "Email"','`IsPrimary` = "yes"');
	#-------------------------------------------------------------------------------
	$Count = DB_Count('ContactsOwners',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count)
		return new gException('USED_BY_LOGIN',SPrintF($Messages['Errors']['OAuth']['EmailUsed'],$Address));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// проверяем что адрес уже не добавлен у этого же юзера
	$Where = Array(SPrintF('`Address` = "%s"',$Address),SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']),'`MethodID` = "Email"');
	#-------------------------------------------------------------------------------
	$AddressCount = DB_Count('ContactsOwners',Array('Where'=>$Where));
	if(Is_Error($AddressCount))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// возможно, адрес скрыт. тогда это добавление ранее удалённого, надо его грохнуть, пусть добавляет
	$Where[] = '`IsHidden` = "yes"';
	#-------------------------------------------------------------------------------
	$AddressCountHidden = DB_Count('ContactsOwners',Array('Where'=>$Where));
	if(Is_Error($AddressCountHidden))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($AddressCountHidden){
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('Contacts',Array('Where'=>$Where));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// обнуляем счётчик, нету такого адерса больше же
		$AddressCount = 0;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если адрес есть, достаём его данные
	if($AddressCount > 0){
		#-------------------------------------------------------------------------------
		$Where = Array(SPrintF('`Address` = "%s"',$Address),SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']),'`MethodID` = "Email"');
		#-------------------------------------------------------------------------------
		$Contact = DB_Select('Contacts',Array('*'),Array('UNIQ','Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Contact)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		// делаем все адреса НЕ-первичными
		$IsUpdate = DB_Update('Contacts',Array('IsPrimary'=>FALSE),Array('Where'=>SPrintF('`UserID` = %u',$Contact['UserID'])));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Contact = Array(
					'CreateDate'	=> Time(),
					'UserID'	=> $GLOBALS['__USER']['ID'],
					'MethodID'	=> 'Email',
					'Address'	=> $Address,
				);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// добавляем исправляем поля
	$Contact['ExternalID']	= SPrintF('%sID:%s',$OAuthName,$ExtUserID);
	$Contact['Confirmed']	= Time();
	$Contact['IsPrimary']	= TRUE;
	$Contact['IsActive']	= TRUE;
	$Contact['UserNotice']	= SPrintF('Добавлено %s OAuth',$OAuthName);
	#-------------------------------------------------------------------------------
	// если есть идентификатор - обновляем, если нету - инзёртим
	if(IsSet($Contact['ID'])){
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Contacts',$Contact,Array('ID'=>$Contact['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Contacts',$Contact);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// правим Email, имя в таблице юзеров
	$IsUpdate = Array('Email'=>$Address);
	if($Name)
		$IsUpdate['Name'] = DB_Escape($Name);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Users',$IsUpdate,Array('ID'=>$Contact['UserID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// фотку юзера можно подсосать...

}else{
	#-------------------------------------------------------------------------------
	// это регистрация, на вход просто ничего тут не делаем
	$Where = Array(SPrintF('`Address` = "%s"',$Address),'`MethodID` = "Email"','`IsPrimary` = "yes"','`IsHidden` = "no"'/*,SPrintF('`ExternalID` = "%sID:%s"',$OAuthName,$ExtUserID)*/);
	#-------------------------------------------------------------------------------
	$Contact = DB_Select('ContactsOwners','*',Array('UNIQ','Where'=>$Where));
	switch(ValueOf($Contact)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		// не найдено, регистрация
		$Password = Comp_Load('Passwords/Generator');
		if(Is_Error($Password))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$User = Comp_Load('www/API/UserRegister',Array('Name'=>($Name)?$Name:SPrintF('%s %s',$OAuthName,$ExtUserID),'Password'=>$Password,'Email'=>$Address,'Message'=>SPrintF('Регистрация через OAuth %s',$OAuthName),'IsInternal'=>TRUE,'ExternalID'=>SPrintF('%sID:%s',$OAuthName,$ExtUserID)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($User)){
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
	case 'array':
		#-------------------------------------------------------------------------------
		// юзер существует, это вход
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// коммитим изменения
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// делаем Logon юзера
$User = Comp_Load('www/API/Logon',Array('Email'=>$Address));
if(Is_Error($User))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это админ, сразу и отлогиниваемся, если нет настройки разрешающей админам вход через внешнюю авторизацию
if($GLOBALS['__USER']['IsAdmin'] && !$Settings['AllowForAdmin']){
	#-------------------------------------------------------------------------------
	$User = Comp_Load('www/API/Logout');
	if(Is_Error($User))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// выводим сообщение, что админам нелья
	return new gException('NOT_ALLOWED_FOR_ADMINs',$Messages['Errors']['OAuth']['OAuthAllowForAdmin']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// всё хорошо, ничего не возвращаем
return Array('UserID'=>$GLOBALS['__USER']['ID']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
