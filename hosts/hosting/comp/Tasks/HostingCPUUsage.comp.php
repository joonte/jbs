<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Tasks']['Types']['HostingCPUUsage'];
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'ExecuteDays'=>@$Settings['ExecuteDays'],'DefaultTime'=>MkTime(10,15,0,Date('n'),Date('j')+1,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 10000','SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
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
$NotifyedCount = 0;
$LockedCount = 0;
$TUsages = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Servers as $Server){
	#-------------------------------------------------------------------------------
	# костыль, чтоб тока один сервер
	#if($Server['Address'] != 's06.host-food.ru')
	#	continue;
	#-------------------------------------------------------------------------------
	$TUsages[$Server['ID']] = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$ClassHostingServer = new HostingServer();
	#-------------------------------------------------------------------------------
	$IsSelected = $ClassHostingServer->Select((integer)$Server['ID']);
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
	$BUsages = Call_User_Func_Array(Array($ClassHostingServer,'GetCPUUsage'),Array($TFilter));
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
	$TUsages[$Server['ID']]['BUsages'] = $BUsages;
	#-------------------------------------------------------------------------------
	# достаём за вчера
	$TFilter = SPrintF('%s - %s',date('Y-m-d',time() - 24*3600),date('Y-m-d',time() - 24*3600));
	$SUsages = Call_User_Func_Array(Array($ClassHostingServer,'GetCPUUsage'),Array($TFilter));
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
	#-------------------------------------------------------------------------------
	$TUsages[$Server['ID']]['SUsages'] = $SUsages;
	#Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: SUsage = %s',print_r($SUsages,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# оцениваем общую нагрузку на сервер
	$ServerUsage = Array('utime'=>0,'stime'=>0);
	#-------------------------------------------------------------------------------
	foreach($SUsages as $SUsage){
		#-------------------------------------------------------------------------------
		$ServerUsage['utime'] = $ServerUsage['utime'] + $SUsage['utime'];
		$ServerUsage['stime'] = $ServerUsage['stime'] + $SUsage['stime'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$TUsages[$Server['ID']]['ServerUsage'] = $ServerUsage;
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: %s: utime = %s%%, stime = %s%%, всего = %s%%',$Server['Address'],Round(($ServerUsage['utime']*100/(24*3600)),2),Round(($ServerUsage['stime']*100/(24*3600)),2),Round((($ServerUsage['utime'] + $ServerUsage['stime'])*100/(24*3600)),2)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем полученные данные
foreach(Array_Keys($TUsages) as $ServerID){
	#-------------------------------------------------------------------------------
	# достаём юзеров из биллинга, и их лимиты
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($TUsages[$ServerID]['BUsages']) as $Login)
		$Array[] = SPrintF("'%s'",$Login);
	#-------------------------------------------------------------------------------
	# однако, массив может получиться пустой
	if(SizeOf($Array) == 0)
		continue;
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$ServerID,Implode(',',$Array));
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'ID','Login','UserID','Domain',
			'(SELECT `QuotaCPU` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `QuotaCPU`',
			'(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `HostingOrdersOwners`.`ServerID`) as `Params`'
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
			# проверяем наличие такого юзера в массиве с нагрузкой
			if(!IsSet($TUsages[$ServerID]['SUsages'][$HostingOrder['Login']]))
				continue;
			#-------------------------------------------------------------------------------
			$ATime = $TUsages[$ServerID]['SUsages'][$HostingOrder['Login']]['utime'] + $TUsages[$ServerID]['SUsages'][$HostingOrder['Login']]['stime'];
			$SUsage = Round($ATime*100 / (24*3600),2);
			#-------------------------------------------------------------------------------
			$BUsage = Round(($TUsages[$ServerID]['BUsages'][$HostingOrder['Login']]['utime'] + $TUsages[$ServerID]['BUsages'][$HostingOrder['Login']]['stime'])*100 / ($Settings['PeriodToLock']*24*3600),2);
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
					'ATime'			=> $ATime,
					'UTime'			=> $TUsages[$ServerID]['SUsages'][$HostingOrder['Login']]['utime'],
					'STime'			=> $TUsages[$ServerID]['SUsages'][$HostingOrder['Login']]['stime'],
					'QuotaCPU'		=> $HostingOrder['QuotaCPU'],
					'QuotaCPUTime'		=> $HostingOrder['QuotaCPU'] * 24 * 60 * 60 / 100,
					'Url'			=> $HostingOrder['Params']['Url'],
					'PeriodToLock'		=> $Settings['PeriodToLock'],
					'UnLockOverlimits'	=> $Settings['UnLockOverlimits'],
					'UnLockOverlimitsTime'	=> $Settings['UnLockOverlimitsTime'],
					'UnLockOverlimitsText'	=> ($Settings['UnLockOverlimits'])?SPrintF("Если вы никак не отреагируете на данное событие, то ваш аккаунт будет автоматически разблокирован в %s.\n\n",$Settings['UnLockOverlimitsTime']):''
					);
			#-------------------------------------------------------------------------------
			# шлём уведомление тем кто превысил порог уведомления, и превысил порог оповещения
			if($SUsage > $HostingOrder['QuotaCPU']*$Settings['NotifyRatio'] && $SUsage > $Settings['LockNotifyFrom']){
				#-------------------------------------------------------------------------------
				$NotifyedCount++;
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
			# если есть превышения за вчера, за неделю, и разрешено лочить
			if($SUsage > $HostingOrder['QuotaCPU']*$Settings['LockRatio']	// вчера превышали
			&& $BUsage > $HostingOrder['QuotaCPU']*$Settings['LockRatio']	// всё время превышали
			&& $BUsage > $Settings['LockBeginFrom']				// всё время - больше чем порог блокировки
			&& $Settings['LockOverlimits']					// разрешено блокироват
			&& Date('d')%$Settings['LockDays'] == 0){			// разрешено лочить именно в этот день
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: Надо лочить: Login = %s; SUsage = %s; BUsage = %s; QuotaCPU = %s',$HostingOrder['Login'],$SUsage,$BUsage,$HostingOrder['QuotaCPU']));
				#-------------------------------------------------------------------------------
				$LockedCount++;
				#-------------------------------------------------------------------------------
				if(!$Settings['CreateTicket']){
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
						break;
						#-------------------------------------------------------------------------------
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>"`Partition` = 'CreateTicket/LOCK_OVERLIMITS'"));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Clause)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: Статья для создания тикета не обнаружена: CreateTicket/LOCK_OVERLIMITS'));
						break;
					case 'array':
						#-------------------------------------------------------------------------------
						# готовим тикет
						$ITicket = Array(
								'Theme'		=> $Clause['Title'],
								'PriorityID'	=> 'Low',
								'Flags'		=> 'CloseOnSee',
								'TargetGroupID'	=> 3100000,
								'TargetUserID'	=> 100,
								'UserID'	=> $HostingOrder['UserID'],
								'Message'	=> TemplateReplace(Strip_Tags($Clause['Text']),$Params,FALSE)
								);
						#-------------------------------------------------------------------------------
						$IsAdd = Comp_Load('www/API/TicketEdit',$ITicket);
						if(Is_Error($IsAdd))
							return ERROR | @Trigger_Error(500);
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
				# время выполнения задачи
				$ExecuteDate = Comp_Load('HostingOrders/SearchExecuteTime');
				if(Is_Error($ExecuteDate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				# лочим 
				$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingSuspend','ExecuteDate'=>$ExecuteDate,'Params'=>Array($HostingOrder['ID'])));
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsAdd)){
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
				# время срабатывания задачи на разблокировку
				$UnLockOverlimitsTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['UnLockOverlimitsTime'],'DefaultTime'=>MkTime(22,0,0,Date('n'),Date('j'),Date('Y'))));
				if(Is_Error($UnLockOverlimitsTime))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				# создаём задачу на разблокировку аккаунта
				$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingActive','ExecuteDate'=>$UnLockOverlimitsTime,'Params'=>Array($HostingOrder['ID'])));
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
if($LockedCount > 0 || $NotifyedCount > 0)
	$GLOBALS['TaskReturnInfo'] = Array(SPrintF('Notifyed: %s',$NotifyedCount),SPrintF('Locked: %s',$LockedCount));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: ExecuteTime = %s',print_r($ExecuteTime,true)));
return $ExecuteTime;
#-------------------------------------------------------------------------------
?>
