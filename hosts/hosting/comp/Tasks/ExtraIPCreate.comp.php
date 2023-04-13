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
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','OrderID','UserID','DependOrderID','SchemeID','ServerID','(SELECT `AddressType` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) AS `AddressType`'),Array('UNIQ','ID'=>$ExtraIPOrderID));
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
# проверяем, активен ли заказ к которому надо прицепить заказ адреса
# вот тока - а надо ли? может заказ ручной какой-то.... думать надо...
# думаю, определять надо позже, в случае если используется АСУ


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
$DependOrder = DB_Select(SPrintF('%sOrdersOwners',$DependService['Code']),Array('ID','UserID','Login','Password','Domain','SchemeID','StatusID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ExtraIPOrder['DependOrderID'])));
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
# JBS-1171 - проверяем статус заказа к которому цепляется адрес
if($DependOrder['StatusID'] == 'OnCreate' || $DependOrder['StatusID'] == 'Waiting'){
	#-------------------------------------------------------------------------------
	# заказ ещё не создан. сдвигаем время запуска на 5 минут
	$GLOBALS['TaskReturnInfo'] = Array(($DependOrder['Login'])=>Array(SPrintF('заказ в статусе %s',$DependOrder['StatusID'])));
	#-------------------------------------------------------------------------------
	return 300;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($DependOrder['StatusID'] == 'Deleted'){
	#-------------------------------------------------------------------------------
	# заказ уже удалён. ошибка
	return ERROR | @Trigger_Error('[comp/Tasks/ExtraIPCreate]: невозможно добавить IP адрес, заказ уже удалён');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# надо определить систему управления и тип панели на сервере, для этого заказа
$ExtraIPServer = new ExtraIPServer();
#-------------------------------------------------------------------------------
$IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrderID,(string)$DependService['Code'],(integer)$DependOrder['ID']);
//$IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrder['OrderID'],(string)$DependService['Code'],(integer)$DependOrder['ID']);
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
$DependScheme = DB_Select(SPrintF('%sSchemes',$DependService['Code']),'*',Array('UNIQ','ID'=>$DependOrder['SchemeID']));
switch(ValueOf($DependScheme)){
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
$IPsPool = Explode("\n",(($DependService['Code'] == 'DS')?($ExtraIPServer->Settings['IPsPool']):($ExtraIPServer->Settings['Params']['IPsPool'])));
#-------------------------------------------------------------------------------
$IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
#-------------------------------------------------------------------------------
$Args = Array($DependOrder['Login'],$ExtraIPOrder['ID'],$DependOrder['Domain'],$IP,$ExtraIPOrder['AddressType']);
#-------------------------------------------------------------------------------
$User = DB_Select('Users','*',Array('UNIQ','ID'=>$ExtraIPOrder['UserID']));
#-------------------------------------------------------------------------------
switch(ValueOf($User)) {
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
$IsCreate = Call_User_Func_Array(Array($ExtraIPServer,'AddIP'),$Args);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCreate)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsCreate;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('ExtraIPOrders',Array('Login'=>$IsCreate['IP']),Array('ID'=>$ExtraIPOrder['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error('[comp/Tasks/ExtraIPCreate]: не удалось прописать IP адрес в базу');
#-------------------------------------------------------------------------------
# вписываем адрес в масив, чтоб не лазить в базу
$ExtraIPOrder['Login'] = $IsCreate['IP'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrder['ID'],'Comment'=>'Дополнительный IP добавлен'));
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> $ExtraIPOrder['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('На сервере (%s) для логина (%s) добавлен дополнительный IP (%s) c обратной зоной (%s)',$ExtraIPServer->Settings['Address'],$DependOrder['Login'],$ExtraIPOrder['Login'],$DependOrder['Domain'])
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = Array(($ExtraIPServer->Settings['Address'])=>Array($DependOrder['Login'],$ExtraIPOrder['Login']));
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
