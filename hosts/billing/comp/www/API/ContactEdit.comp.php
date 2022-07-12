<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContactID	= (integer) @$Args['ContactID'];
$MethodID	=  (string) @$Args['MethodID'];
$Address	=  (string) @$Args['Address'];
$TimeBegin	= (integer) @$Args['TimeBegin'];
$TimeEnd	= (integer) @$Args['TimeEnd'];
$IsActive	= (boolean) @$Args['IsActive'];
$IsSendFiles	= (boolean) @$Args['IsSendFiles'];
$IsImmediately	= (boolean) @$Args['IsImmediately'];
$IsPrimary	= (boolean) @$Args['IsPrimary'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
// делаем сразу в нижнем регистре
$Address = StrToLower($Address);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ContactID){
	#-------------------------------------------------------------------------------
	foreach($GLOBALS['__USER']['Contacts'] as $iContact)
		if($iContact['ID'] == $ContactID)
			$Contact = $iContact;
	#-------------------------------------------------------------------------------
	if(!IsSet($Contact))
		return new gException('CONTACT_ID_NOT_FOUND',SPrintF('Неверно указан идентификатор контакта: %s',$ContactID));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Permission = Permission_Check('ContactRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Contact['UserID']);
	#---------------------------------------------------------------------------
	switch(ValueOf($Permission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем MethodID
$Methods = $Config['Notifies']['Methods'];
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Methods) as $Key)
	$Array[] = $Key;
#-------------------------------------------------------------------------------
if(!In_Array($MethodID,$Array))
	return new gException('WRONG_CONTACT_METHOD','Неправильный или отключённый метод оповещения');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем Address
if(!Preg_Match($Regulars[$MethodID],$Address))
	return new gException('WRONG_CONTACT',SPrintF('Неверно указан адрес: %s',$Address));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`Address` = "%s"',$Address),SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']),SPrintF('`MethodID` = "%s"',$MethodID));
#-------------------------------------------------------------------------------
# проверяем что адрес уже не добавлен у этого же юзера
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
# проверяем что адрес уже не добавлен у этого же юзера
if($ContactID && $Contact['Address'] != $Address && $AddressCount)	// редактирование существующего контакта, смена адреса, но адрес уже есть
	return new gException('ADDRESS_ALREDY_ESISTS',SPrintF('У вас уже есть адрес %s',$Address));
#-------------------------------------------------------------------------------
if(!$ContactID && $AddressCount)	// добавление нового, но адрес уже есть
	return new gException('ADDRESS_ALREDY_ESISTS',SPrintF('У вас уже добавлен адрес %s',$Address));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем IsPrimary
if($MethodID == 'Email' && $Address != $GLOBALS['__USER']['Email'] && $IsPrimary){	// это не текущий первичный адрес, происходит смена
	#-------------------------------------------------------------------------------
	$IsPrimary = FALSE;
	#-------------------------------------------------------------------------------
	if($ContactID && $Contact['Confirmed']){
		#-------------------------------------------------------------------------------
		# проверяем что адрес не стоит первичным у кого-то
		$Count = DB_Count('ContactsOwners',Array('Where'=>SPrintF('`IsPrimary` = "yes" AND `Address` = "%s" AND `UserID` != %u',$Address,$GLOBALS['__USER']['ID'])));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($Count)
			return new gException('ADDRESS_ALREADY_IS_LOGIN',SPrintF('Адрес (%s) уже является логином у другого пользователя',$Address));
		#-------------------------------------------------------------------------------
		$IsPrimary = TRUE;
		#-------------------------------------------------------------------------------
		$Message = SPrintF('Изменён логин для входа в систему %s -> %s',$GLOBALS['__USER']['Email'],$Address);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}elseif($MethodID == 'Email' && $Address != $GLOBALS['__USER']['Email'] && !$IsPrimary){	// добавление нового адреса
	#-------------------------------------------------------------------------------
	$IsPrimary = FALSE;
	#-------------------------------------------------------------------------------
}else{	// а если это текущий первичный адрес, то насрать на галку
	#-------------------------------------------------------------------------------
	$IsPrimary = ($MethodID == 'Email')?TRUE:FALSE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// не даём менять первичный адрес, только вторичные можно
if($ContactID && $Contact['IsPrimary'] && $Contact['Address'] != $Address)
	return new gException('CANNOT_EDIT_PRIMARY_ADDRESS',SPrintF('Логин нельзя редактировать, только поменять на другой из добавленных адресов',$Address));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это первичный контактный адрес, то тип - всегда Email
if($IsPrimary)
	$MethodID = 'Email';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем IsActive
if(!$ContactID || !$Contact['Confirmed'])
	$IsActive = FALSE;
#-------------------------------------------------------------------------------
// на первичный адрес в любом варианте должны приходить уведомления
if($IsPrimary)
	$IsActive = TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContactID)
	$Message = SPrintF('Добавлен контактный адрес (%s) для %s',$Address,$Config['Notifies']['Methods'][$MethodID]['Name']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IContact = Array(
		'MethodID'	=> $MethodID,
		'Address'	=> $Address,
		'TimeBegin'	=> $TimeBegin,
		'TimeEnd'	=> $TimeEnd,
		'IsPrimary'	=> $IsPrimary,
		'IsActive'	=> $IsActive,
		'IsSendFiles'	=> $IsSendFiles,
		'IsImmediately'	=> $IsImmediately,
		);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ContactID){
	#-------------------------------------------------------------------------------
	// логгируем смену контактного адреса, делаем неподтверждённым
	if($Contact['Address'] != $Address){
		#-------------------------------------------------------------------------------
		$Message = SPrintF('Изменён контактный адрес %s: %s -> %s',$MethodID,$Contact['Address'],$Address);
		#-------------------------------------------------------------------------------
		$IContact['Confirmed'] = 0;
		#-------------------------------------------------------------------------------
		$IContact['Confirmation'] = 0;
		#-------------------------------------------------------------------------------
		$IContact['IsActive'] = FALSE;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// обновление делаем внутри транзакции, оно может затронуть более 1 таблицы и менять данные в нескольких строках
	if(Is_Error(DB_Transaction($TransactionID = UniqID('ContactEdit'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# если установлен $IsPrimary - обнуляем все $IsPrimary, прописываем новый адрес в Users
	if($IsPrimary){
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Contacts',Array('IsPrimary'=>FALSE),Array('Where'=>SPrintF('`UserID` = %u',$Contact['UserID'])));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Users',Array('Email'=>$Address),Array('ID'=>$Contact['UserID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Contacts',$IContact,Array('ID'=>$ContactID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$IContact['CreateDate'] = Time();
	#-------------------------------------------------------------------------------
	$IContact['UserID'] = $GLOBALS['__USER']['ID'];
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Contacts',$IContact);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($Message)){
	#-------------------------------------------------------------------------------
	$Event = Array('UserID'=>$GLOBALS['__USER']['ID'],'PriorityID'=>'Billing','Text'=>$Message);
	#-------------------------------------------------------------------------------
	$Event = Comp_Load('Events/EventInsert', $Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
