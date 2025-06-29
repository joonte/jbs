<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/IPMI.SuperMicro.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>'1:00'));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['DSServersIPMI'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// сервера обстукиваем два раза в сутки, ночью и днём. ночью ещё IPMI ребутается
if(!In_Array(Date('G'),Array(2,3,14))){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/DSServersIPMI]: несоответствующее время запуска, пропускаем'));
	#-------------------------------------------------------------------------------
	return $ExecuteTime;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём список выделенных серверов, НЕсломанных
$DSServers = DB_Select('DSSchemes',Array('ID','Name','ILOaddr','ILOuser','ILOpass','(SELECT `UserID` FROM `DSOrdersOwners` WHERE `SchemeID` = `DSSchemes`.`ID` AND `StatusID` = "Active")'),Array('Where'=>'`IsBroken` = "no"','SortOn'=>Array('Name')));
#-------------------------------------------------------------------------------
switch(ValueOf($DSServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return 24*3600;	// нету серверов, сдвигаем задачу на сутки
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
// массив для проблем, вдруг более одной
$ErrorsOut = Array();
#-------------------------------------------------------------------------------
// перебираем выделенные сервера, опрашиваем
foreach($DSServers as $DSServer){
	#-------------------------------------------------------------------------------
	if(!$DSServer['ILOaddr'] || !$DSServer['ILOuser'] || !$DSServer['ILOpass']){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не заданы данные для подключения к IPMI сервера %s',$DSServer['Name']/*,$Status->String*/));
		#-------------------------------------------------------------------------------
		continue;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// два часа ночи, понедельник, перезагружаем IPMI
	if(Date('G') == 2 && Date('N') == 1){
		#-------------------------------------------------------------------------------
		$Result = IPMI_Command($DSServer,'mc reset cold');
		if(Is_Exception($Result))
			Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не удалось выполнить команду перезагрузки IPMI сервера %s',$DSServer['Name']));
		#-------------------------------------------------------------------------------
		// и ничего больше не делаем
		continue;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// в три часа ночи, проставялем верное время
	if(Date('G') == 3){
		#-------------------------------------------------------------------------------
		$Result = IPMI_Command($DSServer,SPrintF('sel time set "%s"',Date('m/d/Y H:i:s')));
		if(Is_Exception($Result))
			Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не удалось установить точное время в IPMI сервера %s',$DSServer['Name']));
		#-------------------------------------------------------------------------------
		// и ничего больше не делаем
		continue;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// информация из IPMI
	$Status = IPMI_StatusGet($DSServer);
	if(Is_Exception($Status)){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не удалось достучаться до IPMI сервера %s',$DSServer['Name']/*,$Status->String*/));
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
				Debug(SPrintF('[comp/Tasks/DSServersIPMI]: IPMI power status %s: %s',$DSServer['Name'],$Value));
			#-------------------------------------------------------------------------------
			if($Value == 'true'){
				#-------------------------------------------------------------------------------
				$Text = SPrintF('обнаружена проблема в IPMI сервера %s, %s: %s',$DSServer['Name'],$Key,$Value);
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DSServersIPMI]: %s',$Text));
				#-------------------------------------------------------------------------------
				$ErrorsOut[] = $Text;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если сейчас два часа ночи, понедельник - выгружаем логи
	if(Date('G') == 2 && Date('N') == 1){
		#-------------------------------------------------------------------------------
		// достаём количество записей в логе
		$NumRecords = IPMI_Sel($DSServer);
		#-------------------------------------------------------------------------------
		if(Is_Exception($NumRecords)){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не удалось получить статус логов сервера %s',$DSServer['Name']));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// а есть ли записи в логе? тогда достаём их
			if($NumRecords['Entries'] > 0){
				#-------------------------------------------------------------------------------
				$Result = IPMI_SelList($DSServer);
				#-------------------------------------------------------------------------------
				if(Is_Exception($Result)){
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/Tasks/DSServersIPMI]: в логе сервера %s найдено %s новых записей, но извлечь их не удлаось',$DSServer['Name'],$NumRecords['Entries']));
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/Tasks/DSServersIPMI]: записи логов из IPMI для сервера %s = %s',$DSServer['Name'],print_r($Result,true)));
					#-------------------------------------------------------------------------------
					// отсылаем лог
					$Message	= "";
					$SendToIDs	= Array();
					#-------------------------------------------------------------------------------
					foreach($Result as $Event)
						$Message = SPrintF("%s\n\n[code]%s[/code]",$Message,$Event);
					#-------------------------------------------------------------------------------
					// если настроено отсылать админам, то ищем весь персонал
					if($Settings['IsAdminNotify']){
						#-------------------------------------------------------------------------------
						$Entrance = Tree_Entrance('Groups',3000000);
						#-------------------------------------------------------------------------------
						switch(ValueOf($Entrance)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------------
							$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',Implode(',',$Entrance))));
							#-------------------------------------------------------------------------------
							switch(ValueOf($Employers)){
							case 'error':
								return ERROR | @Trigger_Error(500);
							case 'exception':
								# No more...
								break;
							case 'array':
								#-------------------------------------------------------------------------------
								foreach($Employers as $Employer)
									if($Employer['ID'] > 2000 || $Employer['ID'] == 100)	// выбираем реальных пользователей
										$SendToIDs[] = $Employer['ID'];
								#-------------------------------------------------------------------------------
								break;
								#-------------------------------------------------------------------------------
							default:
								return ERROR | @Trigger_Error(101);
							}
							#-------------------------------------------------------------------------------
							# No more...
							break;
							#-------------------------------------------------------------------------------
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------------
					}
Debug('line 6');
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// если настроено отсылать юзеру, докидываем и его в массив
					if($Settings['IsUserNotify'] && IsSet($DSServer['UserID']) && !Is_Null($DSServer['UserID']))
						$SendToIDs[] = $DSServer['UserID'];
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// отправляем сообщения
					foreach($SendToIDs as $SendToID){
						#-------------------------------------------------------------------------------
						$IsSend = NotificationManager::sendMsg(new Message('DSOrdersIpmiEvents',(integer)$SendToID,Array('DSServer'=>$DSServer,'Message'=>$Message)));
						#-------------------------------------------------------------------------------
						switch(ValueOf($IsSend)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							# No more...
						case 'true':
							# No more...
							break;
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				// чистим лог
				$Result = IPMI_Command($DSServer,'sel clear');
				if(Is_Exception($Result))
					Debug(SPrintF('[comp/Tasks/DSServersIPMI]: не удалось очистить лог IPMI для сервера %s',$DSServer['Name']));
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DSServersIPMI]: в логе сервера %s нет новых записей',$DSServer['Name']));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
/*
// проверяем, есть ли непрочитанные сообщения
$Count = DB_Count('Events',Array('Where'=>"`IsReaded` != 'yes'"));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// непрочитанных нет, ошибки есть
if(!$Count && SizeOf($ErrorsOut) > 0){
	#-------------------------------------------------------------------------------
	foreach($ErrorsOut as $Text){
		#-------------------------------------------------------------------------------
		$Event = Array('UserID'=>100,'PriorityID'=>'Warning','IsReaded'=>FALSE,'Text'=>$Text);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
 */
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
