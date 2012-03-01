<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$ExtraIPOrders = DB_Select('ExtraIPOrdersOwners','*',Array('Where'=>"`DaysRemainded` IN (1,5,10,15) AND `StatusID` = 'Active'"));
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
      $IsSend = NotificationManager::sendMsg('ExtraIPNoticeDelete',(integer)$ExtraIPOrder['UserID'],Array('ExtraIPOrder'=>$ExtraIPOrder));
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
