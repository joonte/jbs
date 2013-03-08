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
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',FALSE,3600,$Settings['ExecutePeriod']);
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#------------------------------------------------------------------------------
#------------------------------------------------------------------------------
# проверяем наличие серверов вообще
$Count = DB_Count('HostingServers',Array('Where'=>"`IsAutoBalancing` = 'yes'"));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
	Debug("[comp/Tasks/HostingSetPrimaryServer]: no servers for autobalasing, go to next day");
	$GLOBALS['TaskReturnInfo'] = 'No Hosting servers for AutoBalance';
	return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$HostingServersGroups = DB_Select('HostingServersGroups',Array('*'),Array('Where'=>"`FunctionID` != 'NotDefined'"));
switch(ValueOf($HostingServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Tasks/HostingSetPrimaryServer]: no groups with enabled autobalasing, go to next day");
	$GLOBALS['TaskReturnInfo'] = 'No Hosting servers groups with enabled AutoBalance';
	return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
case 'array':
	# All OK, servers found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
foreach($HostingServersGroups as $HostingServersGroup){
	Debug("[comp/Tasks/HostingSetPrimaryServer]: processing Hosting group #" . $HostingServersGroup['ID'] . ", " . $HostingServersGroup['Name']);
	# выбираем все сервера этой группы, где стоит галка про автобалансировку
	$Where = "`IsAutoBalancing` = 'yes' AND `ServersGroupID` = " . $HostingServersGroup['ID'];
	$Columns = Array(
				'ID','BalancingFactor','Address','IsDefault',
				"(SELECT COUNT(*) FROM `HostingOrders` WHERE `ServerID` = `HostingServers`.`ID` AND `StatusID` = 'Active' ) / `BalancingFactor` AS LoadValueActive",
				"(SELECT COUNT(*) FROM `HostingOrders` WHERE `ServerID` = `HostingServers`.`ID` AND (`StatusID` = 'Active' OR `StatusID` = 'Suspended')) / `BalancingFactor` AS LoadValueAll",
			);
	$HostingServers = DB_Select('HostingServers',$Columns,Array('Where'=>$Where));
	switch(ValueOf($HostingServers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# в этой группе нет сервeров с автобалансировкой, ничего не делаем
		Debug("[comp/Tasks/HostingSetPrimaryServer]: group #" . $HostingServersGroup['ID'] . " (" . $HostingServersGroup['Name'] . ") not have AutoBalancing servers");
		break;
	case 'array':
		# высчитываем Primary сервер, в зависимости от алгоритма
		$LA = Array();	// Load Averrage
		$SN = Array();	// Servers Names
		#-------------------------------------------------------------------------------
		if($HostingServersGroup['FunctionID'] == 'ByAllCapacity'){
			#-------------------------------------------------------------------------------
			foreach($HostingServers as $iHostingServer){
				$LA[$iHostingServer['ID']] =  $iHostingServer['LoadValueAll'];
				$SN[$iHostingServer['ID']] =  $iHostingServer['Address'];
				# remember primary server
				if($iHostingServer['IsDefault'])
					$IsDefault = $iHostingServer['Address'];
			}
			#-------------------------------------------------------------------------------
			$MaxLA    = Max($LA);
			#Debug("[comp/Tasks/HostingSetPrimaryServer]: MaxLA = $MaxLA");
			foreach ($LA as $key => $value)
			{
				#Debug("[comp/Tasks/HostingSetPrimaryServer]: $key => $value");
		        	if($value <= $MaxLA){
					$ServerID = $key;
					$MaxLA = $value;
				}
			}
			#-------------------------------------------------------------------------------
		}elseif($HostingServersGroup['FunctionID'] == 'ByActiveCapacity'){
			#-------------------------------------------------------------------------------
			foreach($HostingServers as $iHostingServer){
				$LA[$iHostingServer['ID']] =  $iHostingServer['LoadValueActive'];
				$SN[$iHostingServer['ID']] =  $iHostingServer['Address'];
				# remember primary server
				if($iHostingServer['IsDefault'])
					$IsDefault = $iHostingServer['Address'];
			}
			#-------------------------------------------------------------------------------
			$MaxLA    = Max($LA);
			#Debug("[comp/Tasks/HostingSetPrimaryServer]: MaxLA = $MaxLA");
			foreach ($LA as $key => $value)
			{
				#Debug("[comp/Tasks/HostingSetPrimaryServer]: $key => $value");
		        	if($value <= $MaxLA){
					$ServerID = $key;
					$MaxLA = $value;
				}
			}
			#-------------------------------------------------------------------------------
		}elseif($HostingServersGroup['FunctionID'] == 'ByRandom'){
			foreach($HostingServers as $iHostingServer){
				$LA[] = $iHostingServer['ID'];
				$SN[$iHostingServer['ID']] =  $iHostingServer['Address'];
				# remember primary server
				if($iHostingServer['IsDefault'])
					$IsDefault = $iHostingServer['Address'];
			}
			$ServerKey = Mt_Rand(0, SizeOf($LA) - 1);
			$ServerID = $LA[$ServerKey];
		}else{
			# неизвестный алгоритм / отключено
			Debug(SPrintF("[comp/Tasks/HostingSetPrimaryServer]: group #%u (%s) have unused algoritm: %s",$HostingServersGroup['ID'],$HostingServersGroup['Name'],$HostingServersGroup['FunctionID']));
			break;
		}
		#-------------------------------------------------------------------------------
		# проверяем - сменился ли сервер
		if($IsDefault != $SN[$ServerID]){
			Debug(SPrintF("[comp/Tasks/HostingSetPrimaryServer]: group #%u (%s) primary server is %s, using '%s' algoritm",$HostingServersGroup['ID'],$HostingServersGroup['Name'],$SN[$ServerID],$HostingServersGroup['FunctionID']));
			# обновляем таблицу серверов, выставляем первичный сервер
			$IsUpdate = DB_Update('HostingServers',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`ServersGroupID` = %u',$HostingServersGroup['ID'])));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------
			$IsUpdate = DB_Update('HostingServers',Array('IsDefault'=>TRUE),Array('Where'=> '`ID`=' . $ServerID));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------
			if($Settings['IsEvent']){
				#-------------------------------------------------------------------------------
				$Event = Array(
						'UserID'        => 1,
						'PriorityID'    => 'Hosting',
						'Text'          => SPrintF('Замена основного сервера группы %s (%s=>%s)',$HostingServersGroup['Name'],$IsDefault,$SN[$ServerID])
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
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------

?>
