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
$ServiceID	= (integer) @$Args['ServiceID'];
$Password	=  (string) @$Args['Password'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Password'],$Password))
	return new gException('WRONG_PASSWORD','Неверно указан новый пароль');
#-------------------------------------------------------------------------------
if(StrLen($Password) > 15)
	return new gException('BAD_PASSWORD_LENGTH','Слишком длинный пароль. Максимум - 15 символов.');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsCheck = Comp_Load('Passwords/Checker',$Password,'Change');
if(Is_Error($IsCheck))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/www/API/OrderPasswordChange]: IsCheck = %s',print_r($IsCheck,true)));
if(Is_Exception($IsCheck))
	return $IsCheck;
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
if(Is_Error(System_Load(SPrintF('classes/%sServer.class.php',$Service['Code']))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),Array('*','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Email`'),Array('UNIQ','ID'=>$ServiceOrderID));
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
if($Order['StatusID'] != 'Active')
	return new gException('ORDER_NOT_ACTIVE','Заказ не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
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
$PasswordChange = $ClassServer->PasswordChange($Order['Login'],$Password,Array('Email'=>$Order['Email']));
#-------------------------------------------------------------------------------
switch(ValueOf($PasswordChange)){
case 'error':
	return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
case 'exception':
	return new gException('PASSWORD_CHANGE_ERROR','Ошибка смены пароля',$PasswordChange);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update(SPrintF('%sOrders',$Service['Code']),Array('Password'=>$Password),Array('ID'=>$Order['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Order['Password'] = $Password;
#-------------------------------------------------------------------------------
$ClassName = SPrintF('%sPasswordChangeMsg',$Service['Code']);
#-------------------------------------------------------------------------------
$msg = new $ClassName($Order,(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
$IsSend = NotificationManager::sendMsg($msg);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSend)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
case 'true':
	return Array('Status'=>'Ok');
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
