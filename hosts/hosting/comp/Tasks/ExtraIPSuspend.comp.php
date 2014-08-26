<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ExtraIPOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ExtraIPServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','UserID','ServerID','Login','Domain','(SELECT `IsReselling` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `IsReselling`'),Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ExtraIPServer = new ExtraIPServer();
    #---------------------------------------------------------------------------
    $IsSelected = $ExtraIPServer->Select((integer)$ExtraIPOrder['ServerID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $IsSuspend = $ExtraIPServer->Suspend($ExtraIPOrder['Login'],$ExtraIPOrder['IsReselling']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSuspend)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsSuspend;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $ExtraIPOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ ExtraIP логин (%s), домен (%s) заблокирован на сервере (%s)',$ExtraIPOrder['Login'],$ExtraIPOrder['Domain'],$ExtraIPServer->Settings['Address'])
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
