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
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['OrdersForDelete'];
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'ExecuteDays'=>@$Settings['ExecuteDays'],'DefaultTime'=>MkTime(1,10,0,Date('n'),Date('j')+1,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
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
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
