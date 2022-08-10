<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php','classes/VPSServer.class.php','classes/DNSmanagerServer.class.php','libs/IPMI.SuperMicro.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = Array(
		'`Services`.`ID` = `ServersGroups`.`ServiceID`',
		'(`ServersGroups`.`ID` = `Servers`.`ServersGroupID`)',
		'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 10000 OR (SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 52000 OR (SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 30000',
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'`Servers`.`ID`','Address','`Servers`.`IsActive`','`Servers`.`Params`',
		'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) AS `Name`',
		'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`) AS `Code`',
		);
#-------------------------------------------------------------------------------
$Servers = DB_Select(Array('Servers','ServersGroups','Services'),$Columns,Array('Where'=>$Where,'SortOn'=>Array('ServersGroupID','Address')));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return Time() + 1800;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($Servers as $Server){
	#-------------------------------------------------------------------------------
	//if($Server['Address'] != 'kvm.host-food.ru')
	//	continue;
	#-------------------------------------------------------------------------------
	if(!$Server['IsActive'])
		continue;
	#-------------------------------------------------------------------------------
	# если время последнего опроса задано, и с тех пор прошло меньше 15 минут - пропускаем
	if(IsSet($Server['Params']['LastQuestioning']) && $Server['Params']['LastQuestioning'] > Time() - 1800)
		continue;
	#-------------------------------------------------------------------------------
	$Array[] = $Server;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if(SizeOf($Array) < 1){
	#-------------------------------------------------------------------------------
	// сервера обстукиваем два раза в сутки, ночью и днём. ночью ещё IPMI ребутается
	if(!In_Array(Date('G'),Array(2,16))){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/ServersQuestioning]: все сервера услуг опрошены'));
		#-------------------------------------------------------------------------------
		return Time() + 1800;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/ServersQuestioning]: все сервера услуг опрошены, опрос выделенных серверов'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// достаём список выделенных серверов, НЕсломанных
	$DSServers = DB_Select(Array('DSSchemes'),Array('ID','Name','ILOaddr','ILOuser','ILOpass'),Array('Where'=>'`IsBroken` = "no"','SortOn'=>Array('Name')));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DSServers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return Time() + 1800;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	// массив для проблем, вдруг более одной
	$Out = Array();
	#-------------------------------------------------------------------------------
	// перебираем выделенные сервера, опрашиваем
	foreach($DSServers as $DSServer){
		#-------------------------------------------------------------------------------
		// информация из IPMI
		$Status = IPMI_StatusGet($DSServer);
		if(Is_Exception($Status)){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/ServersQuestioning]: не удалось достучаться до IPMI сервера %s',$DSServer['Name']/*,$Status->String*/));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Status) as $Key){
				#-------------------------------------------------------------------------------
				$Value = $Status[$Key];
				#-------------------------------------------------------------------------------
				if(!$Value)
					continue;
				#-------------------------------------------------------------------------------
				if(Preg_Match('/System\sPower/i',$Key))
					Debug(SPrintF('[comp/Tasks/ServersQuestioning]: IPMI power status %s: %s',$DSServer['Name'],$Value));
				#-------------------------------------------------------------------------------
				if($Value == 'true'){
					#-------------------------------------------------------------------------------
					$Text = SPrintF('обнаружена проблема в IPMI сервера %s, %s: %s',$DSServer['Name'],$Key,$Value);
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/Tasks/ServersQuestioning]: %s',$Text));
					#-------------------------------------------------------------------------------
					$Out[] = $Text;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// если сейчас два часа ночи - перезагружаем IPMI (в это время опрос обычно один раз за час успевает выполнится)
		if(Date('G') == 2){
			#-------------------------------------------------------------------------------
			$Result = IPMI_Command($DSServer,'mc reset cold');
			if(Is_Exception($Result))
				Debug(SPrintF('[comp/Tasks/ServersQuestioning]: не удалось выполнить команду перезагрузки IPMI сервера %s',$DSServer['Name']));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// проверяем, есть ли непрочитанные сообщения
	$Count = DB_Count('Events',Array('Where'=>"`IsReaded` != 'yes'"));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// непрочитанных нет, ошибки есть
	if(!$Count && SizeOf($Out) > 0){
		#-------------------------------------------------------------------------------
		foreach($Out as $Text){
			#-------------------------------------------------------------------------------
			$Event = Array('UserID'=>100,'PriorityID'=>'Warning','IsReaded'=>FALSE,'Text'=>$Text);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Time() + 1800;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/ServersQuestioning]: необходимо опросить серверов: %u',SizeOf($Array)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
$Server = Current($Array);
#-------------------------------------------------------------------------------
if(Is_Null($Server['Name']))
	$Server['Name'] = 'NoGroup';
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['TaskReturnInfo'][$Server['Name']]))
	$GLOBALS['TaskReturnInfo'][$Server['Name']] = Array();
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'][$Server['Name']][] = $Server['Address'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ClassName = SPrintF('%sServer',$Server['Code']);
#-------------------------------------------------------------------------------
$ClassServer = new $ClassName();
#-------------------------------------------------------------------------------
$IsSelected = $ClassServer->Select((integer)$Server['ID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSelected)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'true':
	#-------------------------------------------------------------------------------
	$Users = $ClassServer->GetDomains();
	#-------------------------------------------------------------------------------
	switch(ValueOf($Users)){
	case 'error':
		# No more...
		break 2;
	case 'exception':
		# No more...
		break 2;
	case 'array':
		#-------------------------------------------------------------------------------
		if(Count($Users)){
			#-------------------------------------------------------------------------------
			$Array = Array();
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Users) as $UserID)
				$Array[] = SPrintF("'%s'",$UserID);
			#-------------------------------------------------------------------------------
			$Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$Server['ID'],Implode(',',$Array));
			#-------------------------------------------------------------------------------
			$Orders = DB_Select(SPrintF('%sOrdersOwners',$Server['Code']),Array('ID','OrderID','Login'),Array('Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Orders)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
				break;
			case 'array':
				//Debug(print_r($Orders,true));
				#-------------------------------------------------------------------------------
				foreach($Orders as $Order){
					#-------------------------------------------------------------------------------
					$Parked = $Users[$Order['Login']];
					#-------------------------------------------------------------------------------
					ASort($Parked);
					#-------------------------------------------------------------------------------
					$IOrders = Array('Domain'=>(Count($Parked)?Current($Parked):'not-found'));
					#-------------------------------------------------------------------------------
					// у ВПС обновляем дисковый шаблон
					if($Server['Code'] == 'VPS'){
						#-------------------------------------------------------------------------------
						$IsUpdate = DB_Update('Orders',Array('Params'=>Array('DiskTemplate'=>Current($Parked))),Array('ID'=>$Order['OrderID']));
						if(Is_Error($IsUpdate))
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
					}else{
						#-------------------------------------------------------------------------------
						$IOrders['Parked'] = Implode(',',$Parked);
						#-------------------------------------------------------------------------------
						$IsUpdate = DB_Update(SPrintF('%sOrders',$Server['Code']),$IOrders,Array('ID'=>$Order['ID']));
						if(Is_Error($IsUpdate))
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break 2;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server['Params']['LastQuestioning'] = Time();
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Servers',Array('Params'=>$Server['Params']),Array('ID'=>$Server['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return 30;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
