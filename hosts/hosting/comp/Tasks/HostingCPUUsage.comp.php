<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Tasks']['Types']['HostingCPUUsage'];
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',$Settings['ExecuteTime'],MkTime(10,0,0,Date('n'),Date('j')+1,Date('Y')));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostingServers as $HostingServer){
	#-------------------------------------------------------------------------------
	# костыль, чтоб ткоа один сервер
	if($HostingServer['ID'] != 16)
		continue;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Server = new Server();
	#-------------------------------------------------------------------------------
	$IsSelected = $Server->Select((integer)$HostingServer['ID']);
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
	# достаём за период
	$TFilter = SPrintF('%s - %s',date('Y-m-d',time() - $Settings['PeriodToLock']*24*3600),date('Y-m-d',time() - 24*3600));
	$BUsages = Call_User_Func_Array(Array($Server,'GetCPUUsage'),Array($TFilter));
	#-------------------------------------------------------------------------------
	switch(ValueOf($BUsages)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return $BUsages;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: BUsage = %s',print_r($BUsages,true)));
	#-------------------------------------------------------------------------------
	# достаём за вчера
	$TFilter = SPrintF('%s - %s',date('Y-m-d',time() - 24*3600),date('Y-m-d',time() - 24*3600));
	$SUsages = Call_User_Func_Array(Array($Server,'GetCPUUsage'),Array($TFilter));
	#-------------------------------------------------------------------------------
	switch(ValueOf($SUsages)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return $SUsages;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: SUsage = %s',print_r($SUsages,true)));
	#-------------------------------------------------------------------------------
	# достаём юзеров из биллинга, и их лимиты
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($BUsages) as $Login)
		$Array[] = SPrintF("'%s'",$Login);
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$HostingServer['ID'],Implode(',',$Array));
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'ID','Login','UserID','Domain',
			'(SELECT `QuotaCPU` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `QuotaCPU`',
			'(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Url` FROM `HostingServers` WHERE `HostingServers`.`ID` = `HostingOrdersOwners`.`ServerID`) as `Url`'
			);
	#-------------------------------------------------------------------------------
	$HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($HostingOrders as $HostingOrder){
			#-------------------------------------------------------------------------------
			# проверяем превышение за предыдущий день, если оно есть - то делаем остальное. если нет - не трогаем юзера.
			if(!IsSet($SUsages[$HostingOrder['Login']]))
				continue;
			#-------------------------------------------------------------------------------
			$SUsage = Round(($SUsages[$HostingOrder['Login']]['utime'] + $SUsages[$HostingOrder['Login']]['stime'])*100 / (24*3600),2);
			#-------------------------------------------------------------------------------
			$BUsage = Round(($BUsages[$HostingOrder['Login']]['utime'] + $BUsages[$HostingOrder['Login']]['stime'])*100 / ($Settings['PeriodToLock']*24*3600),2);
			#-------------------------------------------------------------------------------
			# параметры для уведомлений
			$Params = Array(
					'ID'			=> $HostingOrder['ID'],
					'UserID'		=> $HostingOrder['UserID'],
					'Login'			=> $HostingOrder['Login'],
					'Domain'		=> $HostingOrder['Domain'],
					'Scheme'		=> $HostingOrder['Scheme'],
					'SUsage'		=> $SUsage,
					'BUsage'		=> $BUsage,
					'QuotaCPU'		=> $HostingOrder['QuotaCPU'],
					'Url'			=> $HostingOrder['Url'],
					'PeriodToLock'		=> $Settings['PeriodToLock'],
					'UnLockOverlimits'	=> $Settings['UnLockOverlimits'],
					'UnLockOverlimitsPeriod'=> $Settings['UnLockOverlimitsPeriod']
					);
			#-------------------------------------------------------------------------------
			# шлём уведомление тем кто превысил порог уведомления, и превысил порог оповещения
			if($SUsage > $HostingOrder['QuotaCPU']*$Settings['NotifyRatio'] && $SUsage > $Settings['LockNotifyFrom']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: Надо уведомление: Login = %s; SUsage = %s; BUsage = %s; QuotaCPU = %s',$HostingOrder['Login'],$SUsage,$BUsage,$HostingOrder['QuotaCPU']));
				#-------------------------------------------------------------------------------
				$IsSend = NotificationManager::sendMsg(new Message('HostingCPUUsageNotice',$HostingOrder['UserID'],Array('HostingOrder'=>$Params)));
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsSend)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
				case 'true':
					# событие, чтоле прибить...
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$BUsage = Round(($BUsages[$HostingOrder['Login']]['utime'] + $BUsages[$HostingOrder['Login']]['stime'])*100 / ($Settings['PeriodToLock']*24*3600),2);
			#-------------------------------------------------------------------------------
			# если есть превышения за вчера, за неделю, и разрешено лочить
			if($SUsage > $HostingOrder['QuotaCPU']*$Settings['LockRatio'] && $BUsage > $HostingOrder['QuotaCPU']*$Settings['LockRatio'] && $Settings['LockOverlimits']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: Надо лочить: Login = %s; SUsage = %s; BUsage = %s; QuotaCPU = %s',$HostingOrder['Login'],$SUsage,$BUsage,$HostingOrder['QuotaCPU']));
				#-------------------------------------------------------------------------------
				$IsSend = NotificationManager::sendMsg(new Message('HostingCPUUsageNoticeLock',$HostingOrder['UserID'],Array('HostingOrder'=>$Params)));
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsSend)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
				case 'true':
					#-------------------------------------------------------------------------------
					# событие, чтоле прибить...
					#-------------------------------------------------------------------------------
					# блокируем юзера - через триггер, чтобы задания в очередь строились а не разом
					$IsLock = Comp_Load('Triggers/Statuses/HostingOrders/Suspended',$Params);
					if(Is_Error($IsLock))
					        return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					# создаём задачу на разблокировку аккаунта
					$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingActive','ExecuteDate'=>(Time() + $Settings['UnLockOverlimitsPeriod']*3600),'Params'=>Array($HostingOrder['ID'])));
					#-------------------------------------------------------------------------------
					switch(ValueOf($IsAdd)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						# No more...
						break;
					default:
						return ERROR | @Trigger_Error(101);
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
#-------------------------------------------------------------------------------
# запуск в 10 утра
return $ExecuteTime;
#-------------------------------------------------------------------------------
?>
