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
$ServiceOrderID	= (integer) @$Args['ServiceOrderID'];
$OrderID	=  (string) @$Args['OrderID'];
$ServiceID	= (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('ServicesOwners',Array('*'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
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
#-------------------------------------------------------------------------------
// управлять можно не всеми услугами... по уму, надо аттрибут услуги
if(!In_Array($Service['Code'],Array('Hosting','VPS','ISPsw','DNSmanager')))
	return new gException('NO_INTERFACE_FOR_MANAGE','У данной услуги нет автоматического перехода в панель управления');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load(SPrintF('classes/%sServer.class.php',$Service['Code']))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','UserID','StatusID','ServerID','StatusID','DependOrderID',
		SPrintF('(SELECT `UserID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `%sOrdersOwners`.`DependOrderID`) AS `DependOrderUserID`',$Service['Code']),
		SPrintF('(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = (SELECT `ServiceID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `%sOrdersOwners`.`DependOrderID`)) AS `DependOrderCode`',$Service['Code'])
		);
#-------------------------------------------------------------------------------
if(In_Array($Service['Code'], Array('ISPsw','DS'))){
	#-------------------------------------------------------------------------------
	$Columns = Array_Merge($Columns,Array('IP'));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Columns = Array_Merge($Columns,Array('Login','Password'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Where = ($ServiceOrderID?SPrintF('`ID` = %u',$ServiceOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
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
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($Order['StatusID'] != 'Active' && !$__USER['IsAdmin'])
	return new gException('ORDER_NOT_ACTIVE',SPrintF('Заказ услуги "%s" не активен',$Service['NameShort']));
#-------------------------------------------------------------------------------
if($Service['Code'] == 'ISPsw'){
	#-------------------------------------------------------------------------------
	if(!$Order['DependOrderID'])
		return new gException('NO_DEPEND_ORDER_ID','Отсутвует виртуальный/выделенный сервер, для которого заказана эта лицензия');
	// проверяем у юзера права на этот заказ, может не его вообще
	$IsPermission = Permission_Check(SPrintF('%sManage',$Order['DependOrderCode']),(integer)$__USER['ID'],(integer)$Order['DependOrderUserID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
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
	#-------------------------------------------------------------------------------
	// пробуем достать пароль, у заказа к которому относится лицензия
	$DependOrder = DB_Select(SPrintF('%sOrdersOwners',$Order['DependOrderCode']),Array('Password'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['DependOrderID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DependOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		$Password = 'my-mega-pass';
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$Password = $DependOrder['Password'];
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Order = $Order + Array('Login'=>'root','Password'=>$Password);
	#-------------------------------------------------------------------------------
	//Debug(print_r($Order,TRUE));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check(SPrintF('%sManage',$Service['Code']),(integer)$__USER['ID'],(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
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
#-------------------------------------------------------------------------------
$ClassName = SPrintF('%sServer',$Service['Code']);
#-------------------------------------------------------------------------------
$ClassServer = new $ClassName();
#-------------------------------------------------------------------------------
$IsSelected = $ClassServer->Select((integer)$Order['ServerID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSelected)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Params = Array('Login'=>$Order['Login'],'Password'=>$Order['Password']);
#-------------------------------------------------------------------------------
if($Service['Code'] == 'ISPsw')
	$Params = $Params + Array('Url'=>SPrintF('https://%s:1500/ispmgr',$Order['IP']));
#-------------------------------------------------------------------------------
$IsLogon = $ClassServer->Logon($Params);
#-------------------------------------------------------------------------------
switch(ValueOf($IsLogon)){
case 'error':
	return new gException('ERROR_SERVER_ACCESS','Ошибка доступа к серверу');
case 'exception':
	return $IsLogon;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$IsLogon['Status'] = 'Ok';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
//Debug(print_r($IsLogon,true));
return $IsLogon;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
