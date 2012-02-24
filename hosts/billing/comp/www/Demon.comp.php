<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Header('Content-type: text/plain; charset=utf-8');
#-------------------------------------------------------------------------------
$LockID = SPrintF('Cron[%s]',HOST_ID);
#-------------------------------------------------------------------------------
$Free = DB_Query(SPrintF("SELECT IS_FREE_LOCK('%s') as `IsFree`",$LockID));
if(Is_Error($Free))
  return ERROR | @Trigger_Error('[Demon]: не удалось проверить блокировку');
#-------------------------------------------------------------------------------
$Rows = MySQL::Result($Free);
if(Is_Error($Rows))
  return ERROR | @Trigger_Error('[Demon]: не удалось получить данные из запроса');
#-------------------------------------------------------------------------------
if(Count($Rows) < 1)
  return ERROR | @Trigger_Error('[Demon]: неверный результат запроса');
#-------------------------------------------------------------------------------
$Row = Current($Rows);
#-------------------------------------------------------------------------------
if(!$Row['IsFree'])
  return 'Cron already executing (lock is not free)...';
#-------------------------------------------------------------------------------
$Lock = DB_Query(SPrintF("SELECT GET_LOCK('%s',10) as `IsLocked`",$LockID));
if(Is_Error($Lock))
  return ERROR | @Trigger_Error('[Demon]: не удалось установить блокировку');
#-------------------------------------------------------------------------------
$Rows = MySQL::Result($Lock);
if(Is_Error($Rows))
  return ERROR | @Trigger_Error('[Demon]: не удалось получить данные из запроса');
#-------------------------------------------------------------------------------
if(Count($Rows) < 1)
  return ERROR | @Trigger_Error('[Demon]: неверный результат запроса');
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
while(TRUE){
  #-----------------------------------------------------------------------------
  for($i=1;$i<=$Config['Tasks']['PerIteration'];$i++){
    #---------------------------------------------------------------------------
    $Where = SPrintF("`IsActive` = 'yes' AND `IsExecuted` = 'no' AND `ExecuteDate` < UNIX_TIMESTAMP() AND `Errors` < %u",$Config['Tasks']['MaxErrors']);
    #---------------------------------------------------------------------------
    $Task = DB_Select('Tasks', Array('ID','TypeID'), Array('UNIQ','SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where, 'Limits'=>Array('Start'=>0,'Length'=>1)));
    #---------------------------------------------------------------------------
    switch(ValueOf($Task)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break 2;
      case 'array':
        #-----------------------------------------------------------------------
        $Number = Comp_Load('Formats/Task/Number',$Task['ID']);
        if(Is_Error($Number))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        Debug(SPrintF('[comp/www/Cron]: выполнение задания #%s',$Number));
        #-----------------------------------------------------------------------
        $IsExecute = Comp_Load('www/Administrator/API/TaskExecute',Array('TaskID'=>$Task['ID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($IsExecute)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          case 'array':
	    #-------------------------------------------------------------------
	    if(IsSet($GLOBALS['TaskReturnInfo'])){
              if(Is_Array($GLOBALS['TaskReturnInfo'])){
                $GLOBALS['TaskReturnInfo'] = Implode(', ',$GLOBALS['TaskReturnInfo']);
              }
              $AddPrameter = SPrintF('[%s]',$GLOBALS['TaskReturnInfo']);
            }else{
              $AddPrameter = '';
            }
            #-------------------------------------------------------------------
            echo date('Y-m-d',time()) . " in " . date('H:i:s',time()) .  ": Task #" . $Number . " have executed [" . $Task['TypeID'] . "] " . $AddPrameter . "\n";
            #-------------------------------------------------------------------
	    if(IsSet($GLOBALS['TaskReturnInfo']))
	      UnSet($GLOBALS['TaskReturnInfo']);
	    #-------------------------------------------------------------------
          break 2;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
  #-----------------------------------------------------------------------------
  # crutch added by lissyara. With big query, system task may not execute, because
  # it executed last, after all other
  DB_Query("UPDATE `Tasks` SET `CreateDate`=UNIX_TIMESTAMP() WHERE `UserID`=1");
  #-----------------------------------------------------------------------------
  echo date('Y-m-d',time()) . " in " . date('H:i:s',time()) .  ": Waiting " . $Config['Tasks']['SleepTime'] . " seconds...\n\n";
  #-----------------------------------------------------------------------------
  Sleep($Config['Tasks']['SleepTime']);
}
#-------------------------------------------------------------------------------
$FreeLock = DB_Query(SPrintF("SELECT RELEASE_LOCK('%s')",$LockID));
if(Is_Error($FreeLock))
  return ERROR | @Trigger_Error('[Demon]: не удалось сбросить блокировку');
#-------------------------------------------------------------------------------
return Date('r');
#-------------------------------------------------------------------------------

?>
