<?php


#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC'];
#-------------------------------------------------------------------------------
$NowTime = Time();
#$NowTime = MkTime(5,10,0,10,1,11);
$DateTimeArray = getdate($NowTime);
$NowDayOfWeek = $DateTimeArray['wday'];
$NowDayOfMonth = $DateTimeArray['mday'];
#-------------------------------------------------------------------------------
IsSet($Task['Params']['TasksArray'])?$TasksArray = $Task['Params']['TasksArray']:$TasksArray = FALSE;
IsSet($Task['Params']['NowTask'])?$NowTask = $Task['Params']['NowTask']:$NowTask = FALSE;
$IsEnded = FALSE;
#-------------------------------------------------------------------------------
# Формируем массив задач при первом запуске
if(!$TasksArray && !$NowTask){
  Debug("[Tasks/GC]: Формируем массив задач");
  $TasksArray = Array_Keys($Settings['DailyTasks']);
  if($NowDayOfWeek == 1)
    $TasksArray = Array_Merge($TasksArray, Array_Keys($Settings['WeeklyTasks']));
  if($NowDayOfMonth == 1)
    $TasksArray = Array_Merge($TasksArray, Array_Keys($Settings['MonthlyTasks']));
  $Task['Result'] = NULL;
  $TaskResult = NULL;
}
#-------------------------------------------------------------------------------  
Debug(SPrintF("[Tasks/GC]: Массив задач: %s", Implode(', ', $TasksArray)));
#-------------------------------------------------------------------------------  
if($NowTask){
  $TaskCount = 0;
  # Формируем массив параметров для передачи в задачу
#  $TaskParams = Array();
#  foreach(Array_Keys($Settings) as $Key){
#    if(!Is_Array($Settings[$Key]))
#      $TaskParams[$Key] = $Settings[$Key];
  #-----------------------------------------------------------------------------
  # Ищем обработчик и если он есть вызываем его
  if(!Is_Error(System_Element(SPrintF('comp/Tasks/GC/%s.comp.php', $NowTask)))){
    #---------------------------------------------------------------------------
    Debug( SPrintF("[Tasks/GC]: Выполняем %s", $NowTask) );
    $GLOBALS['TaskReturnInfo'] = $NowTask;
    #---------------------------------------------------------------------------
    $__SYSLOG = &$GLOBALS['__SYSLOG'];
    #---------------------------------------------------------------------------
    $Index = Count($__SYSLOG);
    #---------------------------------------------------------------------------
#    $Params = (array)$TaskParams;
#    Array_UnShift($Params,$TaskParams);
#    Array_UnShift($Params,$Path = SPrintF('Tasks/GC/%s',$NowTask));
    $Params = $Settings;
    Array_UnShift($Params,$Settings);
    Array_UnShift($Params,$Path = SPrintF('Tasks/GC/%s',$NowTask));
    #---------------------------------------------------------------------------
    $Result = Call_User_Func_Array('Comp_Load',$Params);
    #---------------------------------------------------------------------------
    $Log = Implode("\n",Array_Slice($__SYSLOG,$Index));
    #---------------------------------------------------------------------------
    switch(ValueOf($Result)){
      case 'error':
        $TaskResult = Array('Task'=>$NowTask,'State'=>'ERROR','Text'=>'Задача вернула ошибку.');
      break;
      case 'true':
        $TaskResult = Array('Task'=>$NowTask,'State'=>'DONE','Text'=>'Задача выполнена.');
      break;
      case 'integer':
        $TaskResult = Array('Task'=>$NowTask,'State'=>'DONE','Text'=>'Задача будет продолжена.');
        $TaskCount = $Result;
      break;
      default:
        $TaskResult = Array('Task'=>$NowTask,'State'=>'EXCEPTION','Text'=>'Задача вернула не предумотренный результат');
    }
    #-----------------------------------------------------------------------------
  }
  else{
    $TaskResult = Array('Task'=>$NowTask,'State'=>'EXCEPTION','Text'=>'Задаче не назначен обработчик.');
  }
  #-----------------------------------------------------------------------------
  # Если задача выполнилась полностью или вызвала ошибку, то выбираем следующую
  if($TaskCount == 0)
    $NowTask = FALSE;
}
# Устанавливаем след. задачу из массива задач
if(!$NowTask && $TasksArray){
  $NowTask = Array_Shift($TasksArray);
  $Task['Params']['TasksArray'] = $TasksArray;
}
# Если массив задач кончился
if(!$NowTask && !$TasksArray){
  Debug("[Tasks/GC]: Задания закончились.");
  #Debug("[Tasks/GC]: След. запуск через сутки.");
  $IsEnded = TRUE;
  $Task['Params'] = Array();
}
#-------------------------------------------------------------------------------
# Формируем результат выполнения задачи
if(IsSet($Task['Result']) && Is_Array($TaskResult)){
  $Task['Result'] .= SPrintF("%s\n", Implode(', ', $TaskResult));
}
else{
  Is_Array($TaskResult)?$Task['Result'] = SPrintF("%s\n", Implode(', ', $TaskResult)):$Task['Result'] = $TaskResult;
}
#-------------------------------------------------------------------------------
# Возвращаемся через сутки ...
if($IsEnded){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('Tasks',Array('Params'=>$Task['Params'],'Result'=>$Task['Result']),Array('ID'=>$Task['ID']));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  return MkTime(4,20,0,Date('n'),Date('j')+1,Date('Y'));
}
else{
  #-----------------------------------------------------------------------------
  $Task['Params']['NowTask'] = $NowTask;
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('Tasks',Array('Params'=>$Task['Params'],'Result'=>$Task['Result']),Array('ID'=>$Task['ID']));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  # ... иначе через 2 мин  
  return 120;
}

?>
