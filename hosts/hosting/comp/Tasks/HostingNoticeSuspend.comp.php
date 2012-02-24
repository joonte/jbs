<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$HostingOrders = DB_Select('HostingOrdersOwners','*',Array('Where'=>"`DaysRemainded` IN (1,5,10,15) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingOrders as $HostingOrder){
      #-------------------------------------------------------------------------
      $IsSend = Notify_Send('HostingNoticeSuspend',(integer)$HostingOrder['UserID'],Array('HostingOrder'=>$HostingOrder));
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSend)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        case 'true':
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
return MkTime(4,20,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
