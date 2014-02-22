<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
//Header('Content-type: text/plain; charset=utf-8');
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
