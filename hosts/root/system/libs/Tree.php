<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Tree_Path($TableID,$RowID,$ColumnsIDs = 'ID'){
  /****************************************************************************/
  $__args_types = Array('string','integer','string,array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Regulars = Regulars();
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['ID'],$TableID))
    return new gException('WRONG_TABLE_ID','Неверный идентификатор таблицы');
  #-----------------------------------------------------------------------------
  $CacheID = SPrintF('Tree_Path[%s]',Md5(SPrintF('%s:%u:%s',$TableID,$RowID,Is_String($ColumnsIDs)?$ColumnsIDs:Implode(':',$ColumnsIDs))));
  #-----------------------------------------------------------------------------
  $Result = MemoryCache_Get($CacheID);
  if(Is_Error($Result)){
    #---------------------------------------------------------------------------
    $Row = DB_Select($TableID,'*',Array('UNIQ','ID'=>$RowID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Row)){
      case 'error':
        return ERROR | @Trigger_Error('[Tree_Path]: не возможно выбрать запись');
      case 'exception':
        return new gException('ROW_NOT_FOUND','Запись не найдена');
      case 'array':
        #-----------------------------------------------------------------------
        if(Is_Array($ColumnsIDs)){
          #---------------------------------------------------------------------
          $Adding = Array();
          #---------------------------------------------------------------------
          foreach($ColumnsIDs as $ColumnID)
            $Adding[$ColumnID] = $Row[$ColumnID];
        }else
          $Adding = $Row[$ColumnsIDs];
        #-----------------------------------------------------------------------
        $Result = Array($Adding);
        #-----------------------------------------------------------------------
        if($Row['ID'] != $Row['ParentID']){
          #---------------------------------------------------------------------
          $Parents = Tree_Path($TableID,(integer)$Row['ParentID'],$ColumnsIDs);
          #---------------------------------------------------------------------
          switch(ValueOf($Parents)){
            case 'error':
              return ERROR | @Trigger_Error('[Tree_Path]: не удалось осуществить рекурсивный вызов');
            case 'exception':
              return ERROR | @Trigger_Error('[Tree_Path]: при рекурсивном вызове произошла ошибка');
            case 'array':
              $Result = Array_Merge($Result,$Parents);
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
        #-----------------------------------------------------------------------
        MemoryCache_Add($CacheID,$Result);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function Tree_Entrance($TableID,$RowID){
  /****************************************************************************/
  $__args_types = Array('string','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Regulars = Regulars();
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['ID'],$TableID))
    return new gException('WRONG_TABLE_ID','Неверный идентификатор таблицы');
  #-----------------------------------------------------------------------------
  $CacheID = SPrintF('Tree_Entrance[%s]',Md5(SPrintF('%s:%u',$TableID,$RowID)));
  #-----------------------------------------------------------------------------
  $Result = MemoryCache_Get($CacheID);
  if(Is_Error($Result)){
    #---------------------------------------------------------------------------
    $Row = DB_Select($TableID,'*',Array('UNIQ','ID'=>$RowID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Row)){
      case 'error':
        return ERROR | @Trigger_Error('[Tree_Entrance]: не возможно найти запись');
      case 'exception':
        return new gException('ROW_NOT_FOUND','Запись не найдена');
      case 'array':
        #-----------------------------------------------------------------------
        $Result = Array($Row['ID']);
        #-----------------------------------------------------------------------
        $Where = SPrintF('`ParentID` = %u AND `ID` != `ParentID`',$Row['ID']);
        #-----------------------------------------------------------------------
        $Childs = DB_Select($TableID,'*',Array('Where'=>$Where));
        #-----------------------------------------------------------------------
        switch(ValueOf($Childs)){
          case 'error':
            return ERROR | @Trigger_Error('[Tree_Entrance]: не возможно найти дочерние записи');
          case 'exception':
            #----->
          break;
          case 'array':
            #-------------------------------------------------------------------
            foreach($Childs as $Child){
              #-----------------------------------------------------------------
              $Entrance = Tree_Entrance($TableID,(integer)$Child['ID']);
              #-----------------------------------------------------------------
              switch(ValueOf($Entrance)){
                case 'error':
                  return ERROR | @Trigger_Error('[Tree_Entrance]: не возможно определить дочерние вхождения записей');
                case 'exception':
                  return ERROR | @Trigger_Error('[Tree_Entrance]: запись оказавшаяся дочерней не найдена');
                case 'array':
                  $Result = Array_Merge($Result,$Entrance);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        MemoryCache_Add($CacheID,$Result);
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function Tree_Parents($TableID,$RowID){
  /****************************************************************************/
  $__args_types = Array('string','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Regulars = Regulars();
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['ID'],$TableID))
    return new gException('WRONG_TABLE_ID','Неверный идентификатор таблицы');
  #-----------------------------------------------------------------------------
  $Row = DB_Select($TableID,'*',Array('UNIQ','ID'=>$RowID));
  #---------------------------------------------------------------------------
  switch(ValueOf($Row)){
    case 'error':
      return ERROR | @Trigger_Error('[Tree_Parents]: не возможно найти запись');
    case 'exception':
      return new gException('ROW_NOT_FOUND','Запись не найдена');
    case 'array':
      #-------------------------------------------------------------------------
      $Query = SPrintF('SELECT * FROM `%s` `TableA` WHERE `ParentID` = %u AND `ID` != `ParentID` AND EXISTS(SELECT * FROM `%s` `TableB` WHERE `TableB`.`ParentID` = `TableA`.`ID`)',$TableID,$Row['ID'],$TableID);
      #-------------------------------------------------------------------------
      $IsQuery = DB_Query($Query);
      if(Is_Error($IsQuery))
        return ERROR | @Trigger_Error('[Tree_Parents]: не возможно найти дочерние записи');
      #-------------------------------------------------------------------------
      $Childs = MySQL::Result($IsQuery);
      #-------------------------------------------------------------------------
      $Result = Array($Row['ID']);
      #-------------------------------------------------------------------------
      foreach($Childs as $Child){
        #-----------------------------------------------------------------------
        $Parents = Tree_Parents($TableID,(integer)$Child['ID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($Parents)){
          case 'error':
            return ERROR | @Trigger_Error('[Tree_Parents]: не возможно определить дочерние вхождения записей');
          case 'exception':
            return ERROR | @Trigger_Error('[Tree_Parents]: запись оказавшаяся дочерней не найдена');
          case 'array':
            $Result = Array_Merge($Result,$Parents);
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      }
      #-------------------------------------------------------------------------
      return $Result;
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
?>