<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
$DeleteTimeout = $Config['Tasks']['Types']['ISPswForDelete']['DeleteTimeout'] * 24 * 3600;
$Where = "`StatusID` = 'Suspended' AND `StatusDate` + $DeleteTimeout - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$ISPswOrders = DB_Select('ISPswOrders','ID',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ISPswOrders as $ISPswOrder){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Deleted','RowsIDs'=>$ISPswOrder['ID'],'Comment'=>'Срок блокировки заказа окончен'));
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
#-------------------------------------------------------------------------------
$StartHour    = $Config['Tasks']['Types']['ISPswForDelete']['StartHour'];
$StartMinutes = $Config['Tasks']['Types']['ISPswForDelete']['StartMinutes'];
#-------------------------------------------------------------------------------
return MkTime($StartHour,$StartMinutes,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
