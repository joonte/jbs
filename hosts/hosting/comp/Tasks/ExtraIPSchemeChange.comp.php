<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ExtraIPOrderID','ExtraIPSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ExtraIPServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','Domain','UserID','OrderID','SchemeID','ServerID','Login'),Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ExtraIPOrderID = (integer)$ExtraIPOrder['ID'];
    #---------------------------------------------------------------------------
    $ExtraIPNewScheme = DB_Select('ExtraIPSchemes','*',Array('UNIQ','ID'=>$ExtraIPOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($ExtraIPNewScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $ExtraIPServer = new ExtraIPServer();
        #-----------------------------------------------------------------------
        $IsSelected = $ExtraIPServer->Select((integer)$ExtraIPOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
	    #-------------------------------------------------------------------
	    $ExtraIPNewScheme['Domain'] = $ExtraIPOrder['Domain'];
            #-------------------------------------------------------------------
            $SchemeChange = $ExtraIPServer->SchemeChange($ExtraIPOrder['Login'],$ExtraIPNewScheme);
            #-------------------------------------------------------------------
            switch(ValueOf($SchemeChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('ExtraIPOrders',Array('SchemeID'=>$ExtraIPSchemeID),Array('ID'=>$ExtraIPOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrderID,'Comment'=>$SchemeChange->String));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $ExtraIPOrder['UserID'],
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Не удалось сменить тарифный план заказу ExtraIP (%s) в автоматическом режиме, причина (%s)',$ExtraIPOrder['Login'],$SchemeChange->String),'PriorityID'=>'Error',
					'IsReaded'	=> FALSE
		    		  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
	              return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    return TRUE;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              case 'true':
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrderID,'Comment'=>'Тарифный план успешно изменен'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
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
      default:
         return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
