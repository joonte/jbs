<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID', 'UserID',
		'(SELECT `Item` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Item`',
		'(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Name`',
		'ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) AS `DaysRemainded`'
		);
$Where = SPrintF("(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) = 'Default' AND `StatusID` = 'Suspended' AND ROUND((UNIX_TIMESTAMP() - `ExpirationDate`)/86400) > %u",$Config['Tasks']['Types']['OrdersForDelete']['DeleteTimeout']);
#-------------------------------------------------------------------------------
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('Deleted %s orders',SizeOf($Orders));
    #---------------------------------------------------------------------------
    foreach($Orders as $Order){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>'Deleted','RowsIDs'=>$Order['ID'],'Comment'=>'Срок блокировки заказа окончен'));
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
$StartHour    = $Config['Tasks']['Types']['OrdersForDelete']['StartHour'];
$StartMinutes = $Config['Tasks']['Types']['OrdersForDelete']['StartMinutes'];
#-------------------------------------------------------------------------------
return MkTime($StartHour,$StartMinutes,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
