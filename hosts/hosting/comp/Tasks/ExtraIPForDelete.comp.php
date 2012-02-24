<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Suspended' AND `StatusDate` + 2592000 - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$ExtraIPOrders = DB_Select('ExtraIPOrders','ID',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ExtraIPOrders as $ExtraIPOrder){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Deleted','RowsIDs'=>$ExtraIPOrder['ID'],'Comment'=>'Срок блокировки заказа окончен'));
      #-------------------------------------------------------------------------
      switch(ValueOf($Comp)){
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
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$StartHour    = $Config['Tasks']['Types']['ExtraIPForDelete']['StartHour'];
$StartMinutes = $Config['Tasks']['Types']['ExtraIPForDelete']['StartMinutes'];
#-------------------------------------------------------------------------------
return MkTime($StartHour,$StartMinutes,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
