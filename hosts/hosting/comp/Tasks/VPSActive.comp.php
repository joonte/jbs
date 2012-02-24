<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','UserID','ServerID','Login','Domain','(SELECT `IsReselling` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`SchemeID`) as `IsReselling`'),Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $VPSServer = new VPSServer();
    #---------------------------------------------------------------------------
    $IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $IsActive = $VPSServer->Active($VPSOrder['Login'],$VPSOrder['IsReselling']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsActive)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsActive;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $VPSOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ VPS логин (%s) успешно активирован на сервере (%s)',$VPSOrder['Login'],$VPSOrder['Domain'],$VPSServer->Settings['Address'])
	                  );
            $Event = Comp_Load('Events/EventInsert',$Event);
            if(!$Event)
              return ERROR | @Trigger_Error(500);
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
