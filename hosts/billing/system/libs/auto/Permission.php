<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Permission_Check($Name,$UserID,$OwnerID = 1){
  /****************************************************************************/
  $__args_types = Array('string','integer','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  if(Is_Error(System_Load('libs/Tree.php')))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Name)
    return new gException('RULE_NAME_IS_EMPTY','Введите имя правила доступа');
  #-----------------------------------------------------------------------------
  $Rules = DB_Select('Permissions','*',Array('Where'=>SPrintF("'%s' LIKE `Name`",DB_Escape($Name)),'SortOn'=>'Metric'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Rules)){
    case 'error':
      return ERROR | @Trigger_Error('[Permission_Check]: не удалось получить права доступа');
    case 'exception':
      return FALSE;
    case 'array':
      #-------------------------------------------------------------------------
      $User = DB_Select('Users',Array('ID','GroupID'),Array('UNIQ','ID'=>$UserID));
      #-------------------------------------------------------------------------
      switch(ValueOf($User)){
        case 'error':
          return ERROR | @Trigger_Error('[Permission_Check]: не удалось выбрать пользователя осуществляющего запрос');
        case 'exception':
          return new gException('USER_NOT_FOUND','Пользователь осуществляющий запрос доступа не найден');
        case 'array':
          #---------------------------------------------------------------------
          $Owner = DB_Select('Users',Array('ID','GroupID','OwnerID','IsManaged'),Array('UNIQ','ID'=>$OwnerID));
          #---------------------------------------------------------------------
          switch(ValueOf($Owner)){
            case 'error':
              return ERROR | @Trigger_Error('[Permission_Check]: не удалось выбрать владельца');
            case 'exception':
              return new gException('OWNER_NOT_FOUND','Владелец объекта не найден');
            case 'array':
              #-----------------------------------------------------------------
              $IsPermission = FALSE;
              #-----------------------------------------------------------------
              foreach($Rules as $Rule){
                #---------------------------------------------------------------
                $Entrance = Tree_Entrance('Groups',(integer)$Rule['UserGroupID']);
                #---------------------------------------------------------------
                switch(ValueOf($Entrance)){
                  case 'error':
                    return ERROR | @Trigger_Error('[Permission_Check]: не удалось определить дочерние группы правила доступа');
                  case 'exception':
                    return new gException('CHILD_GROUPS_PERMISSION_RULE_NOT_FOUND','Дочерние группы правила доступа не определены');
                  case 'array':
                    # No more...
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
                #---------------------------------------------------------------
                if(In_Array($User['GroupID'],$Entrance) || $UserID == $Rule['UserID']){
                  #-------------------------------------------------------------
                  if($User['ID'] != $Owner['ID'] && ($User['ID'] != $Owner['OwnerID'] || !$Owner['IsManaged'])){
                    #-----------------------------------------------------------
                    $Entrance = Tree_Entrance('Groups',(integer)$Rule['OwnerGroupID']);
                    #-----------------------------------------------------------
                    switch(ValueOf($Entrance)){
                      case 'error':
                        return ERROR | @Trigger_Error('[Permission_Check]: не удалось определить дочерние группы правила делегирования');
                      case 'exception':
                        return new gException('CHILD_GROUPS_DELIGATE_RULE_NOT_FOUND','Дочерние группы правила делегирования не определены');
                      case 'array':
                        # No more...
                      break;
                      default:
                        return ERROR | @Trigger_Error(101);
                    }
                    #-----------------------------------------------------------
                    if(In_Array($Owner['GroupID'],$Entrance) || $OwnerID == $Rule['OwnerID']){
                      #---------------------------------------------------------
                      $IsPermission = $Rule['IsAccess'];
                    }
                  }else
                    $IsPermission = $Rule['IsAccess'];
                }
              }
              #-----------------------------------------------------------------
              return $IsPermission;
            default:
              return ERROR | @Trigger_Error(101);
          }
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
?>
