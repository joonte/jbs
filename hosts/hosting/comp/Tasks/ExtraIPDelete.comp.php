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
if(Is_Error(System_Load('classes/ExtraIPServer.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','UserID','Login','OrderType','DependOrderID','SchemeID'),Array('UNIQ','ID'=>$ExtraIPOrderID));
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
    $IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrderID,(string)$ExtraIPOrder['OrderType'],(integer)$ExtraIPOrder['DependOrderID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
	#$Settings,$ExtraIP,$Login,$ID,$IP
        $IsDelete = $ExtraIPServer->DeleteIP($ExtraIPOrder['Login']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsDelete)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return $IsDelete;
          case 'true':
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $ExtraIPOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ выделенного IP (%s), успешно удален с сервера (%s)',$ExtraIPOrder['Login'],$ExtraIPServer->Settings['Address'])
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
