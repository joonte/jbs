<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Active' AND CEIL((`ExpirationDate` - UNIX_TIMESTAMP())/86400) IN (1,5,10,15,30)";
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','UserID','DomainName','ExpirationDate','StatusDate','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`');
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainsOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
      #-------------------------------------------------------------------------
      $msg = new DomainsNoticeSuspendMsg($DomainOrder, (integer)$DomainOrder['UserID']);
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
return MkTime(4,35,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
