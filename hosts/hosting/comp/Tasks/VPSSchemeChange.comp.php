<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID','VPSSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// выбираем данные заказа
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','Login','IP','Password','Domain','UserID','OrderID','SchemeID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `ServerID`','Login','(SELECT `Name` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$VPSOrderID));
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
$VPSOrderID = (integer)$VPSOrder['ID'];
#-------------------------------------------------------------------------------
// выбираем данные старого тарифа
$VPSScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSSchemeID));
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
// выбираем данные нового тарифа
$VPSNewScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSNewScheme)){
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
// достаём информацию о виртуалке
$VMsInfo = $VPSServer->GetVm();
#-------------------------------------------------------------------------------
switch(ValueOf($VMsInfo)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $VMsInfo;
case 'array':
	#-------------------------------------------------------------------------------
	// достаём информацию о конкретной машине - т.к. в $VmInfo сейчас инфа о всех машинах
	foreach($VMsInfo as $VmInfo)
		if($VmInfo['name'] == $VPSOrder['Login'])
			break;
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем не идёт ли миграция машины
if(IsSet($VmInfo['migrate']) && $VmInfo['migrate'] != 'off'){
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = Array(($VPSServer->Settings['Address'])=>Array($VPSOrder['Login'],Trim($VmInfo['migrate'])));
	#-------------------------------------------------------------------------------
	// создаём событие
	$Event = Array(
			'UserID'	=> $VPSOrder['UserID'],
			'PriorityID'	=> 'Hosting',
			'Text'		=> SPrintF('Миграция виртуальной машины %s (%s)',$VPSOrder['Login'],Trim($VmInfo['migrate'])),

			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// возвращаем время через которое таск будет запущен снова
	return 100;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём список активных узлов кластера (этот список - это вариант "любой узел кластера")
$NodeList = $VPSServer->GetNodeList();
#-------------------------------------------------------------------------------
switch(ValueOf($NodeList)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $NodeList;
case 'array':
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('NodeList = %s',print_r($NodeList,true)));
	$Nodes = Array();
	#-------------------------------------------------------------------------------
	// status=1 - запрещено созданеи вируталок, 0 - разрешено
	foreach($NodeList as $Node)
		if(IsSet($Node['id']))
			/* TODO запилить функцию определения активности ноды, или явно врезать в вовзвращаемый массив
			статус ноды для всех либ. сейчас - status - это VmManager, a active - это VeManager */
			if((IsSet($Node['status']) && !$Node['status']) || (IsSet($Node['active']) && $Node['active'] == 'off'))
				$Nodes[] = $Node['name'];
	#-------------------------------------------------------------------------------
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если на новом тарифе явно не заданы ноды, прописываем их
if(!$VPSNewScheme['Node'])
	$VPSNewScheme['Node'] = Implode(',',$Nodes);
#-------------------------------------------------------------------------------
// если на старом тарифе явно не заданы ноды, прописываем их
if(!$VPSScheme['Node'])
	$VPSScheme['Node'] = Implode(',',$Nodes);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем, входит ли текущий узел размещения в узлы нового тариф
if(!In_Array($VmInfo['hostnode'],Explode(',',$VPSNewScheme['Node']))){
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/VPSSchemeChange]: VmInfo[hostnode] = %s',$VmInfo['hostnode']));
	#Debug(SPrintF('[comp/Tasks/VPSSchemeChange]: $VPSNewScheme[Node] = %s',$VPSNewScheme['Node']));
	#-------------------------------------------------------------------------------
	// несовпадаение узлов, выбираем первый узел из массива $Nodes (он с наименьшей загрузкой) совпадающий с узлами куда можно мигрировать
	foreach($NodeList as $Node){
		#-------------------------------------------------------------------------------
		if(In_Array($Node['name'],Explode(',',$VPSNewScheme['Node']))){
			#-------------------------------------------------------------------------------
			$Migrate = $Node['id'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/VPSSchemeChange]: необходима миграция между нодами %s->%s',$VmInfo['hostnode'],$Node['name']));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если требуется миграция, делаем её
if(IsSet($Migrate)){
	#-------------------------------------------------------------------------------
	$IsMigrate = $VPSServer->VmMigrate((integer)$VmInfo['id'],(integer)$Node['id']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsMigrate)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return $IsMigrate;
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/VPSSchemeChange]: запущена миграция между нодами %s->%s',$VmInfo['hostnode'],$Node['name']));
	#-------------------------------------------------------------------------------
	// создаём событие
	$Event = Array(
			'UserID'	=> $VPSOrder['UserID'],
			'PriorityID'	=> 'Hosting',
			'Text'		=> SPrintF('Запущена миграция виртуальной машины %s',$VPSOrder['Login']),
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = Array(($VPSServer->Settings['Address'])=>Array($VmInfo['hostnode'],$Node['name']));
	#-------------------------------------------------------------------------------
	return 100;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSNewScheme['Domain'] = $VPSOrder['Domain'];
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($VPSServer->Settings['Address'])=>Array($VPSOrder['Login'],$VPSOrder['IP']),$VPSOrder['SchemeName']=>Array($VPSNewScheme['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// меняем тариф
$SchemeChange = $VPSServer->SchemeChange($VPSOrder,$VPSNewScheme);
#-------------------------------------------------------------------------------
switch(ValueOf($SchemeChange)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	// ошибка смены, возвращаем старый тариф заказу
	$IsUpdate = DB_Update('VPSOrders',Array('SchemeID'=>$VPSSchemeID),Array('ID'=>$VPSOrderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// проставляем статус, что активен (смена тарифа возможна только на активных заказах)
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>$SchemeChange->String));
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
			'PriorityID'	=> 'Error',
			'Text'		=> SPrintF('Не удалось сменить тарифный план заказу VPS [%s] в автоматическом режиме, причина (%s)',$VPSOrder['Login'],$SchemeChange->String),
			'IsReaded'	=> FALSE

			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
case 'true':
	#-------------------------------------------------------------------------------
	// смена тарифа успешна, проставляем статус
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>'Тарифный план изменен'));
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
			'UserID'        => $VPSOrder['UserID'],
			'PriorityID'    => 'Hosting',
			'Text'          => SPrintF('Успешно изменён тарифный план (%s->%s) заказа на VPS [%s], сервер (%s)',$VPSOrder['SchemeName'],$VPSNewScheme['Name'],$VPSOrder['Login'],$VPSServer->Settings['Address']),
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
