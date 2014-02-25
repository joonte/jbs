<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
//Header('Content-type: text/plain; charset=utf-8');
if(Is_Error(System_Load('libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2012-01-06 in 19:47 MSK, as JBS-260 implemetation
if($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']){
	if($_SERVER['REMOTE_ADDR'] != '127.0.0.1'){
		Debug(SPrintF("[comp/www/Cron]: '%s' is not local IP address",$_SERVER['SERVER_ADDR']));
		return SPrintF('Cron can be run only from server addresses (%s or 127.0.0.1), not from your IP (%s)',$_SERVER['SERVER_ADDR'],$_SERVER['REMOTE_ADDR']);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2014-02-24 in 21:34 MSK, as JBS-819 implemetation
# search all personal
$Entrance = Tree_Entrance('Groups',3000000);
#-------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------
	$String = Implode(',',$Entrance);
	#---------------------------------------------------------------
	$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
	#---------------------------------------------------------------
	switch(ValueOf($Employers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		# send messages to personal
		Debug(SPrintF("[comp/www/Cron]: Need send messages to %s users",SizeOf($Employers)));
		#-------------------------------------------------------------------------------
		foreach($Employers as $Employer){
			#-------------------------------------------------------------------------------
			# select real users
			if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/Cron]: Need send messages to #',(integer)$Employer['ID']));
				#-------------------------------------------------------------------------------
				$Config = &Config();
				#-------------------------------------------------------------------------------
				$Notifies = $Config['Notifies'];
				#-------------------------------------------------------------------------------
				$Methods = Array();
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				foreach(Array_Keys($Notifies['Methods']) as $MethodID)
					if($Notifies['Methods'][$MethodID]['IsActive'])
						$Methods[] = $MethodID;
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				$Replace = Array('Theme'=>'Необходимо изменить тип запуска планировщика','Message'=>SPrintF("В связи с прекращением поддержки запуска планировщика через WEB-интерфейс, требуется перевести его работу в консольный режим запуска. Команда запуска:\nsh %s/scripts/billing/check.cron.run.sh\nЧастота запуска - раз в минуту (он самостоятельно проверяет свою работу и делает перезапуск, при необходимости). Работа текущей версии планировщика будет прекращена через месяц, при следующем релизе.\n\nВ случае любых проблем и вопросов вы можете обратится за технической поддержкой в наш форум:\nhttp://forum.joonte.com/viewforum.php?f=5\n\n",$_SERVER["DOCUMENT_ROOT"]));
				#-------------------------------------------------------------------------------
				$msg = new DispatchMsg($Replace,$Employer['ID'],100);
				#-------------------------------------------------------------------------------
				$IsSend = NotificationManager::sendMsg($msg,$Methods);
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
#-------------------------------------------------------------------------------
$GLOBALS['IsCron'] = TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$LockID = SPrintF('Cron[%s]',HOST_ID);
#-------------------------------------------------------------------------------
$Free = DB_Query(SPrintF("SELECT IS_FREE_LOCK('%s') as `IsFree`",$LockID));
if(Is_Error($Free))
  return ERROR | @Trigger_Error('[Cron]: не удалось проверить блокировку');
#-------------------------------------------------------------------------------
$Rows = MySQL::Result($Free);
if(Is_Error($Rows))
  return ERROR | @Trigger_Error('[Cron]: не удалось получить данные из запроса');
#-------------------------------------------------------------------------------
if(Count($Rows) < 1)
  return ERROR | @Trigger_Error('[Cron]: неверный результат запроса');
#-------------------------------------------------------------------------------
$Row = Current($Rows);
#-------------------------------------------------------------------------------
if(!$Row['IsFree'])
  return 'Cron already executing (lock is not free)...';
#-------------------------------------------------------------------------------
$Lock = DB_Query(SPrintF("SELECT GET_LOCK('%s',10) as `IsLocked`",$LockID));
if(Is_Error($Lock))
  return ERROR | @Trigger_Error('[Cron]: не удалось установить блокировку');
#-------------------------------------------------------------------------------
$Rows = MySQL::Result($Lock);
if(Is_Error($Rows))
  return ERROR | @Trigger_Error('[Cron]: не удалось получить данные из запроса');
#-------------------------------------------------------------------------------
if(Count($Rows) < 1)
  return ERROR | @Trigger_Error('[Cron]: неверный результат запроса');
#-------------------------------------------------------------------------------
$Row = Current($Rows);
#-------------------------------------------------------------------------------
if(!$Row['IsLocked'])
  return 'Cron already executing (can not set lock)...';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Init',100);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$ExecutedIDs = Array();
#-------------------------------------------------------------------------------
$ExecutedIDs[] = -1;
#-------------------------------------------------------------------------------
for($i=1;$i<=$Config['Tasks']['PerIteration'];$i++){
  #-----------------------------------------------------------------------------
  $Where = SPrintF("`ID` NOT IN(%s) AND `IsActive` = 'yes' AND `IsExecuted` = 'no' AND `ExecuteDate` < UNIX_TIMESTAMP() AND `Errors` < %u",Implode(',',$ExecutedIDs),$Config['Tasks']['MaxErrors']);
  #-----------------------------------------------------------------------------
  $Task = DB_Select('Tasks', Array('ID','TypeID'), Array('UNIQ','SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where, 'Limits'=>Array('Start'=>0,'Length'=>1)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Task)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break 2;
    case 'array':
      #-------------------------------------------------------------------------
      $TaskID = $Task['ID'];
      #-------------------------------------------------------------------------
      $Number = Comp_Load('Formats/Task/Number',$TaskID);
      if(Is_Error($Number))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      Debug(SPrintF('[comp/www/Cron]: выполнение задания #%s',$Number));
      #-------------------------------------------------------------------------
      $IsExecute = Comp_Load('www/Administrator/API/TaskExecute',Array('TaskID'=>$TaskID));
      #-------------------------------------------------------------------------
      switch(ValueOf($IsExecute)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        case 'array':
	  if(IsSet($GLOBALS['TaskReturnInfo'])){
	    if(Is_Array($GLOBALS['TaskReturnInfo'])){
              $GLOBALS['TaskReturnInfo'] = Implode(', ',$GLOBALS['TaskReturnInfo']);
	    }
	    $AddPrameter = SPrintF('[%s]',$GLOBALS['TaskReturnInfo']);
	  }else{
	    $AddPrameter = '';
	  }
          #---------------------------------------------------------------------
          echo SPrintF("Task #%s have executed [%s]%s\n",$Number,$Task['TypeID'],$AddPrameter);
          #---------------------------------------------------------------------
	  if(IsSet($GLOBALS['TaskReturnInfo']))
	    UnSet($GLOBALS['TaskReturnInfo']);
          #---------------------------------------------------------------------
          $ExecutedIDs[] = $TaskID;
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$FreeLock = DB_Query(SPrintF("SELECT RELEASE_LOCK('%s')",$LockID));
if(Is_Error($FreeLock))
  return ERROR | @Trigger_Error('[Cron]: не удалось сбросить блокировку');
#-------------------------------------------------------------------------------
return Date('r') . "\n";
#-------------------------------------------------------------------------------

?>
