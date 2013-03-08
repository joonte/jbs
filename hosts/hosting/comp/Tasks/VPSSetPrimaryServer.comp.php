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
if(Is_Error(System_Load('classes/VPSServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Tasks']['Types']['VPSSetPrimaryServer'];
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',FALSE,3600,$Settings['ExecutePeriod']);
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# проверяем наличие серверов вообще
$Count = DB_Count('VPSServers',Array('Where'=>"`IsAutoBalancing` = 'yes'"));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
	Debug("[comp/Tasks/VPSSetPrimaryServer]: no servers for autobalasing, go to next day");
	$GLOBALS['TaskReturnInfo'] = 'No VPS servers for AutoBalance';
	return MkTime(2,50,0,Date('n'),Date('j')+1,Date('Y'));
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где есть сервера для автобалансировки
$VPSServersGroups = DB_Select('VPSServersGroups',Array('*'));
switch(ValueOf($VPSServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	# All OK, servers found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------
foreach($VPSServersGroups as $VPSServersGroup){
	Debug("[comp/Tasks/VPSSetPrimaryServer]: processing VPS group #" . $VPSServersGroup['ID'] . ", " . $VPSServersGroup['Name']);
	# выбираем все сервера этой группы, где стоит галка про автобалансировку
	$VPSServers = DB_Select('VPSServers',Array('*'),Array('Where'=>"`IsAutoBalancing` = 'yes' AND `SystemID` != 'NullSystem' AND `ServersGroupID` = " . $VPSServersGroup['ID']));
	switch(ValueOf($VPSServers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# в этой группе нет сервeров с автобалансировкой, ничего не делаем
		break;
	case 'array':
		$LA = Array();
		# перебираем найденные сервера, опрашиваем, суём нагрузку в массив
		foreach($VPSServers as $iVPSServer){
			$VPSServer = new VPSServer();
			#-------------------------------------------------------------------------
			$IsSelected = $VPSServer->Select((integer)$iVPSServer['ID']);
			#-------------------------------------------------------------------------
			switch(ValueOf($IsSelected)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'true':
				# OK
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------
			#-------------------------------------------------------------------------
			$Usage = $VPSServer->MainUsage();
			switch(ValueOf($Usage)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			default:
				Debug("[comp/Tasks/VPSSetPrimaryServer]: Usage value = " . $Usage);
				# кладём в массив
				$LA[$iVPSServer['ID']] =  $Usage / $iVPSServer['BalancingFactor'];
			}
		}	# enf foreach VPSServers
		Debug(print_r($LA, true));
		#-------------------------------------------------------------------------
		#-------------------------------------------------------------------------
		# находим наименьшее значение в массиве
		$MaxLA    = Max($LA);
		foreach ($LA as $key => $value)
		{
			Debug("[comp/Tasks/VPSSetPrimaryServer]: $key => $value ");
		        if($value <= $MaxLA){
				$ServerID = $key;
				$MaxLA = $value;
			}
		}
		#-------------------------------------------------------------------------
		#-------------------------------------------------------------------------
		# обновляем таблицу серверов, выставляем первичный сервер
		$IsUpdate = DB_Update('VPSServers',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`ServersGroupID` = %u',$VPSServersGroup['ID'])));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$IsUpdate = DB_Update('VPSServers',Array('IsDefault'=>TRUE),Array('ID'=>$ServerID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);

		#-------------------------------------------------------------------------
		foreach($VPSServers as $iVPSServer){
			if($iVPSServer['ID'] == $ServerID){
				$GLOBALS['TaskReturnInfo'][] = $iVPSServer['Address'];
			}
		}
		#-------------------------------------------------------------------------
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------

?>
