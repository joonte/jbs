<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ISPswOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/IspSoft.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# get config values
$Config = Config();
$Settings = $Config['IspSoft']['Settings'];
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('*','(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `ISPswOrdersOwners`.`ContractID`) as `ProfileID`'),Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
        #-----------------------------------------------------------------------
        $ISPswScheme = DB_Select('ISPswSchemes','*',Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($ISPswScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $User = DB_Select('Users','*',Array('UNIQ','ID'=>$ISPswOrder['UserID']));
            #-------------------------------------------------------------------
            switch(ValueOf($User)) {
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                  break;
                case 'array':
                  break;
                default:
                  return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
	    # create license comment
	    if($ISPswScheme['IsInternal']){
	      $LicComment = "INTERNAL, order #" . $ISPswOrder['OrderID'];
	    }else{
	      $LicComment = "EXTERNAL, order #" . $ISPswOrder['OrderID'] . ", for " . $User['Email'];
	    }
	    $ISPswScheme['LicComment'] = $LicComment;
	    # add IP
            $ISPswScheme['IP'] = $ISPswOrder['IP'];
            #-------------------------------------------------------------------
	    $License = IspSoft_Find_Free_License($ISPswScheme);
	    if(Is_Error($License))
	      return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
            if($License){
	      Debug("[comp/Tasks/ISPswCreate]: found free license, elid = " . $License['elid']);
	      $ISPswScheme['elid'] = $License['elid'];
	      $ISPswScheme['LicenseID'] = $License['LicenseID'];
	      # меняем IP лицензии
	      $Change_IP = IspSoft_Change_IP($Settings,$ISPswScheme);
	      if(Is_Error($Change_IP)){
	        return ERROR | @Trigger_Error(500);
	      }else{
	        $IsUpdate = DB_Update('ISPswLicenses',Array('StatusDate'=>Time(),'IsUsed'=>'yes','ip'=>$ISPswScheme['IP']),Array('ID'=>$License['LicenseID']));
		if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
              }
	      # разблокируем
	      if(!IspSoft_UnLock($Settings,$ISPswScheme))
	         return ERROR | @Trigger_Error(500);
              # всё путём, лицензия создана
              $IsCreate = $License;
	    }else{
	      # свободная лицензия не найдена, надо заказывать
	      $IsCreate = IspSoft_Create($Settings,$ISPswScheme);
	      if(Is_Error($IsCreate))
	        return ERROR | @Trigger_Error(500);
	    }
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            switch(ValueOf($IsCreate)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return $IsCreate;
              case 'array':
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrder['ID'],'Comment'=>'ПО успешно заказано'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $IsUpdate = DB_Update('ISPswOrders',Array('LicenseID'=>$IsCreate['LicenseID']),Array('ID'=>$ISPswOrder['ID']));
		    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
		    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $ISPswOrder['UserID'],
					'PriorityID'	=> 'Hosting',
					'Text'		=> SPrintF('Заказ ПО ISPsystem успешно осуществлён, тарифный план (%s), идентификатор пакета (%s)',$ISPswScheme['Name'],$ISPswScheme['PackageID'])
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
