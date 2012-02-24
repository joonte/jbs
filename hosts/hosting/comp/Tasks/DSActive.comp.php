<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DSServer.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','UserID','SchemeID','IP'),Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $DSServer = new DSServer();
    #---------------------------------------------------------------------------
    $IsSelected = $DSServer->Select((integer)$DSOrder['SchemeID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $IsActive = $DSServer->Active($DSOrder['IP']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsActive)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsActive;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $DSOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Арендованный сервер, IP %s, успешно включен',$DSOrder['IP'])
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
