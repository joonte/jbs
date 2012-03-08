<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ISPswOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/IspSoft.php')))
  return ERROR | @Trigger_Error(500);
# get config values
$Config = Config();
$Settings = $Config['IspSoft']['Settings'];
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
        $ISPswScheme['elid']     = $ISPswOrder['elid'];
        $ISPswScheme['LicenseID']= $ISPswOrder['LicenseID'];
        #-----------------------------------------------------------------------
	# блокируем
        if(!IspSoft_Lock($Settings,$ISPswScheme))
          return ERROR | @Trigger_Error(500);
	# удаляем
        if(!IspSoft_Delete($Settings,$ISPswScheme))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> $ISPswOrder['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Заказ ПО ISPsystem (%s), IP адрес (%s) успешно удалён',$ISPswScheme['Name'],$ISPswOrder['IP'])
		      );
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
        #-------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = Array($ISPswOrder['IP'],$ISPswScheme['Name']);
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
