<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ISPswOrderID','ISPswSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/IspSoft.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByService(51000);
#-------------------------------------------------------------------------------
if(!Is_Array($Settings)){
	#-------------------------------------------------------------------------------
	$Exception =  new gException('BILLMANAGER_SETTINGS_NOT_FOUND_2','Дополнения -> Мастера настройки -> Сервера');
	return new gException('BILLMANAGER_SETTINGS_NOT_FOUND_1','Для создания группы ПО необходимо создать группу серверов для сервиса ISPsw, а также активный сервер входящий в эту группу. Для этого, пройдите в следующий раздел биллинговой системы:',$Exception);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('*'),Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ISPswOrderID = (integer)$ISPswOrder['ID'];
    #---------------------------------------------------------------------------
    $ISPswNewScheme = DB_Select('ISPswSchemes','*',Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($ISPswNewScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
            #-------------------------------------------------------------------
            $SchemeChange = IspSoft_Scheme_Change($Settings,$ISPswNewScheme);
            #-------------------------------------------------------------------
            switch(ValueOf($SchemeChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('ISPswOrders',Array('SchemeID'=>$ISPswSchemeID),Array('ID'=>$ISPswOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrderID,'Comment'=>$SchemeChange->String));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $Event = Array(
                                  'UserID'	=> $ISPswOrder['UserID'],
                                  'PriorityID'	=> 'Error',
                                  'Text'	=> SPrintF('Не удалось сменить тарифный план заказу ПО ISPsystem (%s) в автоматическом режиме, причина (%s)',$ISPswOrder['IP'],$SchemeChange->String),
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
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrderID,'Comment'=>'Тарифный план успешно изменен'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $Event = Array(
                                  'UserID'	=> $ISPswOrder['UserID'],
                                  'PriorityID'	=> 'Hosting',
                                  'Text'	=> SPrintF('Тарифный план заказа ПО ISPsystem (%s) успешно изменён на (%s)',$ISPswOrder['IP'],$ISPswNewScheme['Name']),
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
#-------------------------------------------------------------------------------

?>
