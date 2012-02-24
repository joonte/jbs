<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID','VPSSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','Domain','UserID','OrderID','SchemeID','ServerID','Login','(SELECT `Name` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $VPSOrderID = (integer)$VPSOrder['ID'];
    #---------------------------------------------------------------------------
    $VPSNewScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($VPSNewScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $VPSServer = new VPSServer();
        #-----------------------------------------------------------------------
        $IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsSelected)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'true':
	    #-------------------------------------------------------------------
	    $VPSNewScheme['Domain'] = $VPSOrder['Domain'];
	    #-------------------------------------------------------------------
	    $GLOBALS['TaskReturnInfo'] = Array($VPSServer->Settings['Address'],$VPSOrder['Login'],$VPSOrder['SchemeName'],$VPSNewScheme['Name']);
            #-------------------------------------------------------------------
            $SchemeChange = $VPSServer->SchemeChange($VPSOrder['Login'],$VPSNewScheme);
            #-------------------------------------------------------------------
            switch(ValueOf($SchemeChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('VPSOrders',Array('SchemeID'=>$VPSSchemeID),Array('ID'=>$VPSOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>$SchemeChange->String));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $VPSOrder['UserID'],
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Не удалось сменить тарифный план заказу VPS (%s) в автоматическом режиме, причина (%s)',$VPSOrder['Login'],$SchemeChange->String),
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
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>'Тарифный план успешно изменен'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
		    #-----------------------------------------------------------
                    $Event = Array(
                                   'UserID'        => $VPSOrder['UserID'],
                                   'PriorityID'    => 'Hosting',
                                   'Text'          => SPrintF('Успешно изменён тарифный план (%s->%s) заказа на VPS (%s), сервер (%s)',$VPSOrder['SchemeName'],$VPSNewScheme['Name'],$VPSOrder['Login'],$VPSServer->Settings['Address']),
                                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
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
