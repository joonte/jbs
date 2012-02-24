<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','HostingOrderID','HostingSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',Array('ID','UserID','OrderID','SchemeID','ServerID','Login','(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $HostingOrderID = (integer)$HostingOrder['ID'];
    #---------------------------------------------------------------------------
    $HostingNewScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($HostingNewScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Server = new Server();
        #-----------------------------------------------------------------------
        $IsSelected = $Server->Select((integer)$HostingOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
	    #-------------------------------------------------------------------
	    $GLOBALS['TaskReturnInfo'] = Array($Server->Settings['Address'],$HostingOrder['Login'],$HostingOrder['SchemeName'],$HostingNewScheme['Name']);
            #-------------------------------------------------------------------
            $SchemeChange = $Server->SchemeChange($HostingOrder['Login'],$HostingNewScheme);
            #-------------------------------------------------------------------
            switch(ValueOf($SchemeChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('HostingOrders',Array('SchemeID'=>$HostingSchemeID),Array('ID'=>$HostingOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Active','RowsIDs'=>$HostingOrderID,'Comment'=>$SchemeChange->String));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $HostingOrder['UserID'],
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Не удалось сменить тарифный план заказу хостинга (%s) в автоматическом режиме, причина (%s)',$HostingOrder['Login'],$SchemeChange->String),
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
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Active','RowsIDs'=>$HostingOrderID,'Comment'=>'Тарифный план успешно изменен'));
		#---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $Event = Array(
					'UserID'        => $HostingOrder['UserID'],
					'PriorityID'    => 'Hosting',
					'Text'          => SPrintF('Успешно изменён тарифный план (%s->%s) заказа на хостинг (%s), сервер (%s)',$HostingOrder['SchemeName'],$HostingNewScheme['Name'],$HostingOrder['Login'],$Server->Settings['Address']),
				);
		    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #---------------------------------------------------------------
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
