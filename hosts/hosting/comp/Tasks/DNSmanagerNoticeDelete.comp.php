<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Suspended' AND ROUND((UNIX_TIMESTAMP() - `StatusDate`)/86400) IN (2,3,6,11,16,21,31,41,51,61,71,101)";
#-------------------------------------------------------------------------------
$DNSmanagerOrders = DB_Select('DNSmanagerOrdersOwners',Array('ID','UserID','OrderID','Login','StatusDate'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('Notified %u accounts',SizeOf($DNSmanagerOrders));
    #---------------------------------------------------------------------------
    foreach($DNSmanagerOrders as $DNSmanagerOrder){
      #-------------------------------------------------------------------------
      $IsSend = NotificationManager::sendMsg(new DNSmanagerNoticeDeleteMsg($DNSmanagerOrder, (integer)$DNSmanagerOrder['UserID']));
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
return MkTime(4,25,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
