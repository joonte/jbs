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
$Columns = Array(
			'*',
			'(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `ISPswOrdersOwners`.`ContractID`) as `ProfileID`',
			'(SELECT `elid` FROM `ISPswLicenses` WHERE `ISPswOrdersOwners`.`LicenseID`=`ISPswLicenses`.`ID`) AS `elid`'
		);
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $ISPswScheme = DB_Select('ISPswSchemes','*',Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
    #-----------------------------------------------------------------------
    switch(ValueOf($ISPswScheme)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
	$ISPswScheme['elid']	 = $ISPswOrder['elid'];
	$ISPswScheme['LicenseID']= $ISPswOrder['LicenseID'];
        #-----------------------------------------------------------------------
	# разблокируем
        if(!IspSoft_UnLock($Settings,$ISPswScheme))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> $ISPswOrder['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Заказ ПО ISPsystem (%s), IP адрес (%s) успешно активирован',$ISPswScheme['Name'],$ISPswOrder['IP'])
		      );
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
        #-------------------------------------------------------------------
        return TRUE;
      default:
        return ERROR | @Trigger_Error(101);
      }	# end of ISPswScheme
  default:
    return ERROR | @Trigger_Error(101);
}  # end of ISPswOrder
#-------------------------------------------------------------------------------

?>
