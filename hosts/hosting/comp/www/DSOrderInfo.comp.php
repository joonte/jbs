<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Null($Args))
	if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/IPMI.SuperMicro.php')))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DSOrderID = (integer) @$Args['DSOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `IPaddr` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `IPaddr`',
			'(SELECT `OS` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `OS`',
			'(SELECT `DSuser` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `DSuser`',
			'(SELECT `DSpass` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `DSpass`',
			'(SELECT `ILOaddr` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOaddr`',
			'(SELECT `ILOuser` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOuser`',
			'(SELECT `ILOpass` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOpass`',
			'(SELECT `CPU` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `CPU`',
			'(SELECT `ram` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ram`',
			'(SELECT `raid` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `raid`',
			'(SELECT `disks` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `disks`',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` =  (SELECT `ServerID` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`))) as `ServersGroupName`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DSOrdersOwners`.`OrderID`) AS `UserNotice`',
			'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DSOrdersOwners`.`OrderID`) AS `AdminNotice`',
			'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DSOrdersOwners`.`ContractID`) AS `Customer`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `DSOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',$Columns,Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
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
$IsPermission = Permission_Check('DSOrdersRead',(integer)$__USER['ID'],(integer)$DSOrder['UserID']);
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
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$DSOrder['OrderID']);
if(Is_Error($Number))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Заказ выделенного сервера #%s/%s',$Number,$DSOrder['IP']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Table[] = Array('Номер',$Number);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$DSOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$DSOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$DSOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',SPrintF('%s (%s)',$DSOrder['Scheme'],$DSOrder['ServersGroupName']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'IP адреса';
#-------------------------------------------------------------------------------
$Table[] = Array('Первичный IP адрес',$DSOrder['IP']);
#-------------------------------------------------------------------------------
if($DSOrder['ExtraIP'])
	$Table[] = Array('Дополнительные IP адреса',new Tag('PRE',Array('class'=>'Standard'),$DSOrder['ExtraIP']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$DSOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = '-Технические характеристики сервера';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Процессор',$DSOrder['CPU']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Объём оперативной памяти, Gb',$DSOrder['ram']);
#-------------------------------------------------------------------------------
$Table[] = Array('Тип RAID контроллера',$DSOrder['raid']);
#-------------------------------------------------------------------------------
$Table[] = Array('Характеристики жёстких дисков',$DSOrder['disks']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Данные для доступа';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DSOrder['IPaddr'])
	$Table[] = Array('IP адрес сервера',$DSOrder['IPaddr']);
#-------------------------------------------------------------------------------
if(In_Array($DSOrder['StatusID'],Array('OnCreate','Active','Suspended')) || $GLOBALS['__USER']['IsAdmin'] ){
	#-------------------------------------------------------------------------------
	if($DSOrder['OS'])
		$Table[] = Array('Предустановленная ОС',$DSOrder['OS']);
	#-------------------------------------------------------------------------------
	if($DSOrder['DSuser'])
		$Table[] = Array('Пользователь ОС',$DSOrder['DSuser']);
	#-------------------------------------------------------------------------------
	if($DSOrder['DSpass'])
		$Table[] = Array('Пароль пользователя ОС',$DSOrder['DSpass']);
	#-------------------------------------------------------------------------------
	if($DSOrder['ILOaddr']){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/String',$DSOrder['ILOaddr'],35,Preg_Match('/^http/ui',$DSOrder['ILOaddr'])?$DSOrder['ILOaddr']:NULL /*,$DSOrder['ILOaddr']*/);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Адрес IPMI/iLO',new Tag('TD',Array('class'=>'Standard'),$Comp));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if($DSOrder['ILOuser'])
		$Table[] = Array('Пользователь IPMI/iLO',$DSOrder['ILOuser']);
	#-------------------------------------------------------------------------------
	if($DSOrder['ILOpass'])
		$Table[] = Array('Пароль IPMI/iLO',$DSOrder['ILOpass']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// информация из IPMI
	$Status = IPMI_StatusGet($DSOrder);
	if(Is_Exception($Status)){
		#-------------------------------------------------------------------------------
		#return new gException('IPMI_StatusGet',$Status->String);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Table[] = 'Общая информация из IPMI';
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Status) as $Key){
			#-------------------------------------------------------------------------------
			$Value = $Status[$Key];
			#-------------------------------------------------------------------------------
			if(!$Value)
				continue;
			#-------------------------------------------------------------------------------
			if(In_Array($Value,Array('on','off'))){
				#-------------------------------------------------------------------------------
				$Table[] = new Tag('TR',new Tag('TD',Array('align'=>'right'),$Key),new Tag('TD',Array('class'=>'Head','style'=>SPrintF('background:%s;',($Value == 'on')?'lightgreen':'orange')),($Value == 'on')?'Включён':'Выключен'));
				#-------------------------------------------------------------------------------
			}elseif(In_Array($Value,Array('false','true'))){
				#-------------------------------------------------------------------------------
				$Table[] = new Tag('TR',new Tag('TD',Array('align'=>'right'),$Key),new Tag('TD',Array('class'=>'Head','style'=>SPrintF('background:%s;',($Value == 'false')?'lightgreen':'red')),($Value == 'false')?'Нет':'Да'));
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$Table[] = Array($Key,$Value);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// информация из IPMI, сенсоры
	$Status = IPMI_SDR($DSOrder);
	if(Is_Exception($Status)){
		#-------------------------------------------------------------------------------
		#return new gException('IPMI_SDR',$Status->String);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Table[] = 'Данные сенсоров из IPMI';
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Status) as $Key){
			#-------------------------------------------------------------------------------
			if($Status[$Key][1] == 'ns')
				continue;
			#-------------------------------------------------------------------------------
			$Value = $Status[$Key][0];
			$Color = ($Status[$Key][1] == 'ok')?'lightgreen':'red';
			#-------------------------------------------------------------------------------
			if(!$Value)
				continue;
			#-------------------------------------------------------------------------------
			$Table[] = new Tag('TR',new Tag('TD',Array('align'=>'right'),$Key),new Tag('TD',Array('class'=>'Head','style'=>SPrintF('background:%s;',$Color)),$Value));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// надо ещё строк 10 последних с лога дёргать: sel list last 10
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','DSOrders',$DSOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DSOrder['UserNotice'] || ($DSOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($DSOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$DSOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($DSOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$DSOrder['AdminNotice']));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
