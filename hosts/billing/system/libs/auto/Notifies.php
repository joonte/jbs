<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Notify_Send($TypeID,$UserID,$Replace = Array(),$FromID = 100){
  /****************************************************************************/
  $__args_types = Array('string','integer','array','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Executor)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      $Replace['Executor'] = $Executor['Attribs'];
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
  #-----------------------------------------------------------------------------
  $Config = Config();
  #-----------------------------------------------------------------------------
  $Notifies = $Config['Notifies'];
  #-----------------------------------------------------------------------------
  $Index = 0;
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Notifies['Methods']) as $MethodID){
    #---------------------------------------------------------------------------
    if(!$Notifies['Methods'][$MethodID]['IsActive'])
      continue;
    #---------------------------------------------------------------------------
    $Count = DB_Count('Notifies',Array('Where'=>SPrintF("`UserID` = %u AND `MethodID` = '%s' AND `TypeID` = '%s'",$UserID,$MethodID,$TypeID)));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($Count)
      continue;
    #---------------------------------------------------------------------------
    if(Is_Error(System_Load(SPrintF('libs/%s.php',$MethodID))))
      return ERROR | @Trigger_Error('[Notify_Send]: библиотека метода оповещения не найдена');
    #---------------------------------------------------------------------------
    $Function = SPrintF('%s_Send',$MethodID);
    #---------------------------------------------------------------------------
    $Result = Call_User_Func($Function,$TypeID,$UserID,$Replace,$FromID);
    #---------------------------------------------------------------------------
    switch(ValueOf($Result)){
      case 'error':
        return ERROR | @Trigger_Error(SPrintF('[Notify_Send]: в функции (%s) оповещения произошла критическая ошибка',$Function));
      case 'exception':
        # No more...
      break;
      case 'true':
        $Index++;
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
  #-----------------------------------------------------------------------------
  if($Index < 1)
    return new gException('USER_NOT_NOTIFIED','Не удалось оповестить пользователя ни одним из методов');
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
?>
