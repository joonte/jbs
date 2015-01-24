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
$ServiceOrderID = (integer) @$Args['ServiceOrderID'];
$ServiceID      = (integer) @$Args['ServiceID'];
$Password       =  (string) @$Args['Password'];
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
if(Is_Error(System_Load(SPrintF('classes/%sServer.class.php',$Service['Code']))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','Login','Password','StatusID','ServerID','StatusID');
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),$Columns,Array('UNIQ','ID'=>$ServiceOrderID));
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
if($Order['StatusID'] != 'Active')
	return new gException('ORDER_NOT_ACTIVE',SPrintF('Заказ услуги "%s" не активен',$Service['NameShort']));
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
$IsLogon = $ClassServer->Logon(Array('Login'=>$Order['Login'],'Password'=>$Order['Password']));
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
return $IsLogon;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
