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
if(Is_Error(System_Load('classes/DSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','UserID','IP','SchemeID'),Array('UNIQ','ID'=>$DSOrderID));
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
        $IsDelete = $DSServer->Delete($DSOrder['IP']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsDelete)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsDelete;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $DSOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Арендованый сервер IP %s успешно отключен ',$DSOrder['IP'])
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
