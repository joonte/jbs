<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
$DeleteTimeout = $Config['Tasks']['Types']['HostingForDelete']['DeleteTimeout'] * 24 * 3600;
$Where = "`StatusID` = 'Suspended' AND `StatusDate` + $DeleteTimeout - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$HostingOrders = DB_Select('HostingOrders','ID',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('Deleted %s accounts',SizeOf($HostingOrders));
    #---------------------------------------------------------------------------
    foreach($HostingOrders as $HostingOrder){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Deleted','RowsIDs'=>$HostingOrder['ID'],'Comment'=>'Срок блокировки заказа окончен'));
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
$StartHour    = $Config['Tasks']['Types']['HostingForDelete']['StartHour'];
$StartMinutes = $Config['Tasks']['Types']['HostingForDelete']['StartMinutes'];
#-------------------------------------------------------------------------------
return MkTime($StartHour,$StartMinutes,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
