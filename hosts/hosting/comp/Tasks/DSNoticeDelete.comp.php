<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Suspended' AND ROUND((`StatusDate` + 1296000 - UNIX_TIMESTAMP())/86400) IN (1,2,3,4,5,10)";
#-------------------------------------------------------------------------------
$DSOrders = DB_Select('DSOrdersOwners',Array('ID','UserID','IP','StatusDate'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DSOrders as $DSOrder){
      #-------------------------------------------------------------------------
      $IsSend = NotificationManager::sendMsg(new Message('DSNoticeDelete',(integer)$DSOrder['UserID'],Array('DSOrder'=>$DSOrder)));
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
