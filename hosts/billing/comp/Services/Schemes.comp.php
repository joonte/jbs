<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('TableID','UserID','GroupBy','UniqID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','GroupID'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User )){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Path = Tree_Path('Groups',(integer)$User['GroupID'],'ID');
    #---------------------------------------------------------------------------
    switch(ValueOf($Path)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        Array_UnShift($Path,$User['ID']);
        #-----------------------------------------------------------------------
        $Result = Array();
        #-----------------------------------------------------------------------
        for($i=Count($Path)-1;$i>=0;$i--){
          #---------------------------------------------------------------------
          $GroupID = $Path[$i];
          #---------------------------------------------------------------------
          $Where = ($i?SPrintF('`GroupID` = %u',$GroupID):SPrintF('`UserID` = %u',$GroupID));
          #---------------------------------------------------------------------
          $Schemes = DB_Select($TableID,'*',Array('Where'=>$Where));
          #---------------------------------------------------------------------
          switch(ValueOf($Schemes)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            break;
            case 'array':
              #-----------------------------------------------------------------
              foreach($Schemes as $Scheme){
                #---------------------------------------------------------------
                $KeyID = Array();
                #---------------------------------------------------------------
                foreach($GroupBy as $ColumnID)
                  $KeyID[] = $Scheme[$ColumnID];
                #---------------------------------------------------------------
                $Result[Implode(':',$KeyID)] = $Scheme;
              }
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
        #-----------------------------------------------------------------------
        if(!Is_Null($UniqID)){
          #---------------------------------------------------------------------
          $IsQuery = DB_Query(SPrintF('SHOW COLUMNS FROM `%s`',$TableID));
          if(Is_Error($IsQuery))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Rows = MySQL::Result($IsQuery);
          if(Is_Error($Rows))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if(Count($Rows) < 1)
            return ERROR | @Trigger_Error(400);
          #---------------------------------------------------------------------
          $Columns = Array();
          #---------------------------------------------------------------------
          foreach($Rows as $Row)
            $Columns[] = SPrintF('%s %s',$Row['Field'],$Row['Type']);
          #---------------------------------------------------------------------
          $Create = SPrintF('CREATE TEMPORARY TABLE `%s` (%s) ENGINE=MEMORY DEFAULT CHARSET=utf8',$UniqID,Implode(',',$Columns));
          #---------------------------------------------------------------------
          $IsQuery = DB_Query($Create);
          if(Is_Error($IsQuery))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          foreach($Result as $Scheme){
            #-------------------------------------------------------------------
            $IsInsert = DB_Insert($UniqID,$Scheme);
            if(Is_Error($IsInsert))
              return ERROR | @Trigger_Error(500);
          }
        }
        #-----------------------------------------------------------------------
        return $Result;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
