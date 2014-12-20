<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
#------------------------------------------------------------------------------
$Config = Config();
#------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['HostingSetPrimaryServer'];
#------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#------------------------------------------------------------------------------
#------------------------------------------------------------------------------
# проверяем наличие серверов хостинга с включенной автобалансировкой
$Servers = DB_Select('Servers',Array('ID','Params'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 10000','SortOn'=>'Address'));
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#------------------------------------------------------------------------------
	Debug("[comp/Tasks/HostingSetPrimaryServer]: no hosting servers, go to next day");
	$GLOBALS['TaskReturnInfo'] = 'No Hosting servers';
	return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
	#------------------------------------------------------------------------------
	break;
	#------------------------------------------------------------------------------
case 'array':
	#------------------------------------------------------------------------------
	$Count = 0;
	#------------------------------------------------------------------------------
	foreach($Servers as $Server)
		if($Server['Params']['IsAutoBalancing'])
			$Count++;
	#-------------------------------------------------------------------------------
	if($Count < 1){
		#-------------------------------------------------------------------------------
		Debug("[comp/Tasks/HostingSetPrimaryServer]: no servers for autobalasing, go to next day");
		$GLOBALS['TaskReturnInfo'] = 'No Hosting servers for AutoBalance';
		#-------------------------------------------------------------------------------
		return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$ServersGroups = DB_Select('ServersGroups',Array('*'),Array('Where'=>"`FunctionID` != 'NotDefined' AND `ServiceID` = 10000"));
switch(ValueOf($ServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	Debug("[comp/Tasks/HostingSetPrimaryServer]: no groups with enabled autobalasing, go to next day");
	$GLOBALS['TaskReturnInfo'] = 'No Hosting servers groups with enabled AutoBalance';
	return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
	#-------------------------------------------------------------------------------
case 'array':
	# All OK, servers found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
foreach($ServersGroups as $ServersGroup){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/HostingSetPrimaryServer]: processing group #%s, %s',$ServersGroup['ID'],$ServersGroup['Name']));
	# выбираем все сервера этой группы, где стоит галка про автобалансировку
	$Columns = Array(
			'ID','Address','IsDefault','Params',
			'(SELECT COUNT(*) FROM `OrdersOwners` WHERE `ServerID` = `Servers`.`ID` AND `StatusID` = "Active") AS `AccountsActive`',
			'(SELECT COUNT(*) FROM `OrdersOwners` WHERE `ServerID` = `Servers`.`ID` AND (`StatusID` = "Active" OR `StatusID` = "Suspended")) AS AccountsAll'
			);


	#-------------------------------------------------------------------------------
	$Servers = DB_Select('Servers',$Columns,Array('Where'=>SPrintF('`IsActive` = "yes" AND `ServersGroupID` = %u',$ServersGroup['ID'])));
	switch(ValueOf($Servers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		# в этой группе нет активных сервeров, ничего не делаем
		Debug(SPrintF('[comp/Tasks/HostingSetPrimaryServer]: group #%u (%s) not have active servers',$ServersGroup['ID'],$ServersGroup['Name']));
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Servers) as $Key)
			if(!$Servers[$Key]['Params']['IsAutoBalancing'])
				UnSet($Servers[$Key]);
		#Debug(SPrintF('[comp/Tasks/HostingSetPrimaryServer]: Servers = %s',print_r($Servers,true)));
		#-------------------------------------------------------------------------------
		# высчитываем Primary сервер, в зависимости от алгоритма
		$LA = Array();	// Load Averrage
		$SN = Array();	// Servers Names
		#-------------------------------------------------------------------------------
		if($ServersGroup['FunctionID'] == 'ByAllCapacity'){
			#-------------------------------------------------------------------------------
			foreach($Servers as $Server){
				#-------------------------------------------------------------------------------
				$LA[$Server['ID']] =  $Server['AccountsAll'] / $Server['Params']['BalancingFactor'];
				#-------------------------------------------------------------------------------
				$SN[$Server['ID']] =  $Server['Address'];
				#-------------------------------------------------------------------------------
				# remember primary server
				if($Server['IsDefault'])
					$IsDefault = $Server['Address'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$MaxLA    = Max($LA);
			#Debug("[comp/Tasks/HostingSetPrimaryServer]: MaxLA = $MaxLA");
			#-------------------------------------------------------------------------------
			foreach ($LA as $key => $value)
			{
				#-------------------------------------------------------------------------------
				#Debug("[comp/Tasks/HostingSetPrimaryServer]: $key => $value");
		        	if($value <= $MaxLA){
					#-------------------------------------------------------------------------------
					$ServerID = $key;
					#-------------------------------------------------------------------------------
					$MaxLA = $value;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}elseif($ServersGroup['FunctionID'] == 'ByActiveCapacity'){
			#-------------------------------------------------------------------------------
			foreach($Servers as $Server){
				#-------------------------------------------------------------------------------
				$LA[$Server['ID']] =  $Server['AccountsActive'] / $Server['Params']['BalancingFactor'];
				#-------------------------------------------------------------------------------
				$SN[$Server['ID']] =  $Server['Address'];
				#-------------------------------------------------------------------------------
				# remember primary server
				if($Server['IsDefault'])
					$IsDefault = $Server['Address'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$MaxLA    = Max($LA);
			#Debug("[comp/Tasks/HostingSetPrimaryServer]: MaxLA = $MaxLA");
			#-------------------------------------------------------------------------------
			foreach ($LA as $key => $value)
			{
				#-------------------------------------------------------------------------------
				#Debug("[comp/Tasks/HostingSetPrimaryServer]: $key => $value");
		        	if($value <= $MaxLA){
					#-------------------------------------------------------------------------------
					$ServerID = $key;
					#-------------------------------------------------------------------------------
					$MaxLA = $value;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}elseif($ServersGroup['FunctionID'] == 'ByRandom'){
			#-------------------------------------------------------------------------------
			foreach($Servers as $Server){
				#-------------------------------------------------------------------------------
				$LA[] = $Server['ID'];
				#-------------------------------------------------------------------------------
				$SN[$Server['ID']] =  $Server['Address'];
				#-------------------------------------------------------------------------------
				# remember primary server
				if($Server['IsDefault'])
					$IsDefault = $Server['Address'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$ServerKey = Mt_Rand(0, SizeOf($LA) - 1);
			#-------------------------------------------------------------------------------
			$ServerID = $LA[$ServerKey];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			# неизвестный алгоритм / отключено
			Debug(SPrintF("[comp/Tasks/HostingSetPrimaryServer]: group #%u (%s) have unused algoritm: %s",$ServersGroup['ID'],$ServersGroup['Name'],$ServersGroup['FunctionID']));
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# проверяем - сменился ли сервер
		if(!IsSet($IsDefault) || $IsDefault != $SN[$ServerID]){
			Debug(SPrintF("[comp/Tasks/HostingSetPrimaryServer]: group #%u (%s) primary server is %s, using '%s' algoritm",$ServersGroup['ID'],$ServersGroup['Name'],$SN[$ServerID],$ServersGroup['FunctionID']));
			# обновляем таблицу серверов, выставляем первичный сервер
			$IsUpdate = DB_Update('Servers',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroup['ID'])));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------
			$IsUpdate = DB_Update('Servers',Array('IsDefault'=>TRUE),Array('ID'=>$ServerID));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------
			if($Settings['IsEvent']){
				#-------------------------------------------------------------------------------
				$Event = Array(
						'UserID'        => 1,
						'PriorityID'    => 'Hosting',
						'Text'          => SPrintF('Замена основного сервера группы %s (%s=>%s)',$ServersGroup['Name'],(IsSet($IsDefault)?$IsDefault:'UnSeted'),$SN[$ServerID])
						);
				#-------------------------------------------------------------------------------
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
		#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'][] = $SN[$ServerID];
		#-------------------------------------------------------------------------------
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	UnSet($IsDefault);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------

?>
