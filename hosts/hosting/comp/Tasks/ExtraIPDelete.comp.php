<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ExtraIPOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ExtraIPServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','UserID','Login','DependOrderID','SchemeID','ServerID','(SELECT `Name` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
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

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# информация о заказе к которому прицеплен IP, если она есть...
$DependService = DB_Select(Array('Servers','ServersGroups'),Array('(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`) AS `Code`', '(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`)'),Array('UNIQ','Where'=>Array('`Servers`.`ServersGroupID` = `ServersGroups`.`ID`',SPrintF('`Servers`.`ID` = %u',$ExtraIPOrder['ServerID']))));
#-------------------------------------------------------------------------------
switch(ValueOf($DependService)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DESTINATION_SERVER_NOT_FOUND','Сервер размещения не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# находим данные для этого заказа Хостинга/ВПС
$DependOrder = DB_Select(SPrintF('%sOrdersOwners',$DependService['Code']),Array('ID','UserID','Login','Password','Domain','SchemeID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ExtraIPOrder['DependOrderID'])));
switch(ValueOf($DependOrder)){
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
$ExtraIPServer = new ExtraIPServer();
#-------------------------------------------------------------------------------
$IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrderID,(string)$DependService['Code'],(integer)$DependOrder['ID']);
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
$IsDelete = $ExtraIPServer->DeleteIP($ExtraIPOrder['Login']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsDelete)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsDelete;
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $ExtraIPOrder['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF('Заказ выделенного IP (%s), удален с сервера (%s)',$ExtraIPOrder['Login'],$ExtraIPServer->Settings['Address'])
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($ExtraIPServer->Settings['Address'])=>Array($ExtraIPOrder['Login']));
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
