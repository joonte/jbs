<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','HostingOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','ServerID','Login','Domain','(SELECT `IsReselling` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `IsReselling`','(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `SchemeName`');
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Server = new Server();
    #---------------------------------------------------------------------------
    $IsSelected = $Server->Select((integer)$HostingOrder['ServerID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $IsSuspend = $Server->Suspend($HostingOrder['Login'],$HostingOrder['IsReselling']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSuspend)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsSuspend;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $HostingOrder['UserID'],
				'PriorityID'	=> 'Hosting',
				'Text'		=> SPrintF('Заказ хостинга логин [%s], домен (%s), тариф (%s) успешно заблокирован на сервере (%s)',$HostingOrder['Login'],$HostingOrder['Domain'],$HostingOrder['SchemeName'],$Server->Settings['Address'])
	                  );
            $Event = Comp_Load('Events/EventInsert',$Event);
            if(!$Event)
               return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
	    $GLOBALS['TaskReturnInfo'] = Array($Server->Settings['Address'],$HostingOrder['Login'],$HostingOrder['SchemeName']);
            #-------------------------------------------------------------------
            return TRUE;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
