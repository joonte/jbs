<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// данные заказа
$Columns = Array(
		'ID','OrderID','UserID','Login','IP','Domain','SchemeID','Password',
		'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `ServerID`',
		'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `Params`',
		'(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `VPSOrdersOwners`.`ContractID`) AS `ProfileID`'
		);
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
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
$VPSServer = new VPSServer();
#-------------------------------------------------------------------------------
// выбираем данные сервера
$IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
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
// данные тарифа
$VPSScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSScheme)){
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
Debug(SPrintF('[comp/Tasks/VPSCreate]: передано DiskTemplate = (%s)',IsSet($VPSOrder['Params']['DiskTemplate'])?$VPSOrder['Params']['DiskTemplate']:'не задан'));
#-------------------------------------------------------------------------------
// резервное значение шаблона
$RescueTemplate = 'FreeBSD 13';
#-------------------------------------------------------------------------------
// если задан шаблон, то проверяем есть ли такой шаблон
if(IsSet($VPSOrder['Params']['DiskTemplate'])){
	#-------------------------------------------------------------------------------
	foreach(Explode("\n",$VPSServer->Settings['Params']['DiskTemplate']) as $Line){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/VPSCreate]: Line = (%s)',print_r($Line,true)));
		#-------------------------------------------------------------------------------
		// FreeBSD 13:4000=фря 13, надо 4 гига
		// вырезаем описания через =
		$Line1 = Explode('=',Trim($Line));
		#-------------------------------------------------------------------------------
		// отрезаем размер
		$Template = Explode(':',Trim($Line1[0]));
		#-------------------------------------------------------------------------------
		if($Template[0] == $VPSOrder['Params']['DiskTemplate'])
			$DiskTemplate = $Template[0];
		#-------------------------------------------------------------------------------
		// сохраняем резервное значение, на всякий случай
		if(In_Array($Template[0],Array('CentOS 7','Alma Linux 8','Rocky Linux 8')))
			$RescueTemplate = $Template[0];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
/* вообще, это более не актуально - так как размер задан в шаблонах
 * непонятно почему я это оставил, так что комментирум
// проверяем что лимиты достаточны для установки винды, если шаблон винды
if(IsSet($DiskTemplate) && Preg_Match('/Windows/',$DiskTemplate)){
	#-------------------------------------------------------------------------------
	if($VPSScheme['SchemeParams']['InternalName']['HDD'] < 20000 || $VPSScheme['SchemeParams']['InternalName']['RAM'] < 1024){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/VPSCreate]: Выбран шаблон Windows, но он не пролезает под минимальные требования'));
		#-------------------------------------------------------------------------------
		$DiskTemplate = '';
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
*/
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/VPSCreate]: DiskTemplate = (%s)',IsSet($DiskTemplate)?$DiskTemplate:'не задан'));
#-------------------------------------------------------------------------------
// если шаблон не задан - выбираем любой линукс или фрю
if(!IsSet($DiskTemplate) || StrLen($DiskTemplate) < 2){
	#-------------------------------------------------------------------------------
	//$DiskTemplates = Explode("\n",$VPSServer->Settings['Params']['DiskTemplate']);
	#-------------------------------------------------------------------------------
	//$Template = Explode('=',Trim($DiskTemplates[0]));
	#-------------------------------------------------------------------------------
	//$DiskTemplate = $Template[0];
	#-------------------------------------------------------------------------------
	$DiskTemplate = $RescueTemplate;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/VPSCreate]: DiskTemplate = (%s)',print_r($DiskTemplate,true)));
#-------------------------------------------------------------------------------
$VPSOrder['DiskTemplate'] = $DiskTemplate;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если не стоит автовыбор ноды - то надо ткнуться к кластеру, достать список нод
if($VPSScheme['Node']){
	#-------------------------------------------------------------------------------
	$NodeList = $VPSServer->GetNodeList();
	#-------------------------------------------------------------------------------
	switch(ValueOf($NodeList)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return $NodeList;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/VPSCreate]: NodeList = %s',print_r($NodeList,true)));
	#-------------------------------------------------------------------------------
	if(SizeOf($NodeList) < 1)
		return ERROR | @Trigger_Error('[comp/Tasks/VPSCreate]: не удалось получить список нод кластера');
	#-------------------------------------------------------------------------------
	// список нод на которые будет размещаться заказ
	$Nodes = Array();
	#-------------------------------------------------------------------------------
	// перебираем ноды кластера, проверяем, есть ли указанная в тарифе нода(-ы) в списке
	foreach(Array_Keys($NodeList) as $Node)
		if(In_Array($Node,Explode(',',$VPSScheme['Node'])))
			$Nodes[] = $NodeList[$Node]['id'];
	#-------------------------------------------------------------------------------
	// если ноды есть, выбираем одну, первую, т.к. ноды отсортированы по возрастанию занятости
	if(SizeOf($Nodes) > 0){
		#-------------------------------------------------------------------------------
		$VPSScheme['Node'] = $Nodes[0];
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// ноды не найдены
		return ERROR | @Trigger_Error(SPrintF('[comp/Tasks/VPSCreate]: заданные тарифом ноды (%s) не найдены в списке нод кластера (%s)',$VPSScheme['Node'],Implode(',',Array_Keys($NodeList))));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	// нода не задана, автовыбор
	$VPSScheme['Node'] = 'auto';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// это вот не то чтобы работает.... наследие создания заказа на хостинг
$IPsPool = Explode("\n",$VPSServer->Settings['Params']['IPsPool']);
#-------------------------------------------------------------------------------
$IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args = Array($VPSOrder,$IP,$VPSScheme);
#-------------------------------------------------------------------------------
// создаём виртуалку
$IsCreate = Call_User_Func_Array(Array($VPSServer,'Create'),$Args);
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
// вносим IP адрес в базу
$IsUpdate = DB_Update('VPSOrders',Array('IP'=>$IsCreate['IP']),Array('ID'=>$VPSOrder['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error('[comp/Tasks/VPSCreate]: не удалось прописать IP адрес для виртуального сервера');
#-------------------------------------------------------------------------------
// вписываем адрес в массив, чтоб не лазить в базу
$VPSOrder['IP'] = $IsCreate['IP'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проставляем статус
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrder['ID'],'Comment'=>'Заказ создан на сервере'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
// создаём событие
$Event = Array(
		'UserID'	=> $VPSOrder['UserID'],
		'PriorityID'	=> 'Hosting',
		'Text'		=> SPrintF('Заказ VPS [%s] создан на сервере (%s) с тарифным планом (%s), идентификатор пакета (%s)',$VPSOrder['IP'],$VPSServer->Settings['Address'],$VPSScheme['Name'],$VPSScheme['PackageID'])
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($VPSServer->Settings['Address'])=>Array($VPSOrder['Login'],$VPSOrder['IP'],$VPSScheme['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
