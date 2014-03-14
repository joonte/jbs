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
if(Is_Error(System_Load('classes/Server.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Tasks']['Types']['VPSSetPrimaryServer'];
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# проверяем наличие серверов вообще
$Count = DB_Count('Servers',Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) = 30000'));
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
$ServersGroups = DB_Select('ServersGroups',Array('*'),Array('Where'=>'`ServiceID` = 30000'));
switch(ValueOf($ServersGroups)){
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
foreach($ServersGroups as $ServersGroup){
	Debug(SPrintF("[comp/Tasks/VPSSetPrimaryServer]: processing VPS group #%s, %s",$ServersGroup['ID'],$ServersGroup['Name']));
	# выбираем все сервера этой группы, где стоит галка про автобалансировку
	$Where = Array("`IsAutoBalancing` = 'yes'","`SystemID` != 'NullSystem'",SPrintF('`ServersGroupID` = %u',$ServersGroup['ID']));
	$Servers = DB_Select('Servers',Array('*'),Array('Where'=>$Where));
	switch(ValueOf($Servers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# в этой группе нет сервeров с автобалансировкой, ничего не делаем
		break;
	case 'array':
		$LA = Array();
		# перебираем найденные сервера, опрашиваем, суём нагрузку в массив
		foreach($Servers as $iServer){
			$Server = new Server();
			#-------------------------------------------------------------------------
			$IsSelected = $Server->Select((integer)$iServer['ID']);
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
			$Usage = $Server->MainUsage();
			switch(ValueOf($Usage)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			default:
				Debug(SPrintF('[comp/Tasks/VPSSetPrimaryServer]: Usage value = %s',$Usage));
				# кладём в массив
				$LA[$iServer['ID']] =  $Usage / $iServer['BalancingFactor'];
			}
		}	# enf foreach Servers
		#Debug(print_r($LA, true));
		#-------------------------------------------------------------------------
		#-------------------------------------------------------------------------
		# находим наименьшее значение в массиве
		$MaxLA    = Max($LA);
		foreach ($LA as $key => $value)
		{
			Debug(SPrintF('[comp/Tasks/VPSSetPrimaryServer]: %s => %s',$key,$value));
		        if($value <= $MaxLA){
				$ServerID = $key;
				$MaxLA = $value;
			}
		}
		#-------------------------------------------------------------------------
		#-------------------------------------------------------------------------
		# обновляем таблицу серверов, выставляем первичный сервер
		$IsUpdate = DB_Update('Servers',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroup['ID'])));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$IsUpdate = DB_Update('Servers',Array('IsDefault'=>TRUE),Array('ID'=>$ServerID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);

		#-------------------------------------------------------------------------
		foreach($Servers as $iServer){
			if($iServer['ID'] == $ServerID){
				$GLOBALS['TaskReturnInfo'][] = $iServer['Address'];
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
