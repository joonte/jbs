<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Suspended' AND ROUND((`StatusDate` + 2678400 - UNIX_TIMESTAMP())/86400) IN (1,5,10,15,20,25)";
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainOrdersOwners','*',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = Array('Notified'=>Array(SizeOf($DomainOrders)));
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
      #-------------------------------------------------------------------------
      $msg = new DomainNoticeDeleteMsg($DomainOrder, (integer)$DomainOrder['UserID']);
      $IsSend = NotificationManager::sendMsg($msg);
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
return MkTime(4,40,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
