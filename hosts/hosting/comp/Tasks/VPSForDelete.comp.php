<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
//$Where = "`StatusID` = 'Suspended' AND `StatusDate` + 2592000 - UNIX_TIMESTAMP() <= 0";
$Config = Config();
$DeleteTimeout = $Config['Tasks']['Types']['VPSForDelete']['DeleteTimeout'] * 24 * 3600;
$Where = "`StatusID` = 'Suspended' AND `StatusDate` + $DeleteTimeout - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$VPSOrders = DB_Select('VPSOrders','ID',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = SPrintF('Deleted %s accounts',SizeOf($VPSOrders));
    #---------------------------------------------------------------------------
    foreach($VPSOrders as $VPSOrder){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Deleted','RowsIDs'=>$VPSOrder['ID'],'Comment'=>'Срок блокировки заказа окончен'));
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
$StartHour    = $Config['Tasks']['Types']['VPSForDelete']['StartHour'];
$StartMinutes = $Config['Tasks']['Types']['VPSForDelete']['StartMinutes'];
#-------------------------------------------------------------------------------
return MkTime($StartHour,$StartMinutes,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
