<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ISPswOrderID	= (integer) @$Args['ISPswOrderID'];
$LicenseID	= (integer) @$Args['LicenseID'];
$IP		=  (string) @$Args['IP'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/IspSoft.php')))
  return ERROR | @Trigger_Error(500);
# get config values
$Config = Config();
$Settings = $Config['IspSoft']['Settings'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['IP'],$IP))
  return new gException('WRONG_PASSWORD','Неверно указан новый IP адрес');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','UserID','StatusID','IP','(SELECT `ISPtype` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID`=`ISPswSchemes`.`ID`) AS `ISPtype`','(SELECT `Email` FROM `Users` WHERE `ISPswOrdersOwners`.`UserID` = `Users`.`ID` ) AS `OwnerEmail`');
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($ISPswOrder['StatusID'] != 'Active')
      return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ ПО не активен');
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    if($ISPswOrder['IP'] == $IP)
      return new gException('OLD_IP_MATCH_WITH_NEW_IP','IP адреса не могут совпадать');
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswManage',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        # проверяем, можно ли менять IP для этой лицензии
        # 1. внутренним, могут менять только сотрудники
        # 2. время - прошёл ли 31 день от последней смены адреса
	$ISPswLicense = DB_Select('ISPswLicenses',Array('*'),Array('UNIQ','Where'=>"IP='" . $ISPswOrder['IP'] . "' AND `ISPtype`='" . $ISPswOrder['ISPtype'] . "'"));
	switch(ValueOf($ISPswLicense)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		# license found
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-----------------------------------------------------------------------
	if(!$__USER['IsAdmin'] && $ISPswLicense['IsInternal'])
	return new gException('INTERNAL_LICENSE','Данная лицензия предназначена для использования на заказах VPS и выделенных серверов. Вы не можете изменить её IP адрес. Если вам нужна лицензия для другого заказа VPS или выделенного сервера - сделайте заказ на новую лицензию.');
	#-----------------------------------------------------------------------
	$m_time = $ISPswLicense['StatusDate'] + 31 * 24 * 3600 - Time();
	if($m_time > 0){
		$Comp = Comp_Load('Formats/Date/Remainder', $m_time);
		return new gException('LICENSE_PERIOD_NOT_EXCESSED','IP адрес лицензии можно менять один раз в месяц. До момента когда его можно будет сменить, осталось ' . $Comp);
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('ISPswChangeIP'))))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if($ISPswLicense['IsInternal']){
		$LicComment = "INTERNAL, order #" . $ISPswOrder['OrderID'];
	}else{
		$LicComment = "EXTERNAL, order #" . $ISPswOrder['OrderID'] . ", for " . $ISPswOrder['OwnerEmail'];
	}
	#-----------------------------------------------------------------------
	$ISPswScheme = array(
				'ISPtype'	=> $ISPswOrder['ISPtype'],
				'IP'		=> $IP,
				'LicComment'	=> $LicComment
			);
	#-----------------------------------------------------------------------
	# если лицензия внутренняя - ищщем свободную, или заказываем новую
	if($ISPswLicense['IsInternal']){
		#-----------------------------------------------------------------------
	        $elid = IspSoft_Find_Free_License($ISPswScheme);
	        if($elid){
			Debug("[comp/Tasks/ISPswCreate]: found free license, elid = " . $elid);
	                $ISPswScheme['elid'] = $elid;
	                # меняем IP лицензии
	                if(IspSoft_Change_IP($Settings,$ISPswScheme)){
	                $IsQuery = DB_Query("UPDATE `ISPswLicenses` SET `UpdateDate`=UNIX_TIMESTAMP(), `IsUsed`='yes', `ip`='" . $ISPswScheme['IP'] . "' WHERE `elid`=" . $elid);
	                if(Is_Error($IsQuery))
	                  return ERROR | @Trigger_Error(500);
	        	}else{
				return ERROR | @Trigger_Error(500);
			}
			# разблокируем
			if(!IspSoft_UnLock($Settings,$ISPswScheme))
				return ERROR | @Trigger_Error(500);
			# всё путём, лицензия создана
			$IsCreate = TRUE;
		}else{
			# свободная лицензия не найдена, надо заказывать
			$IsCreate = IspSoft_Create($Settings,$ISPswScheme);
			if($IsCreate){
				# помечаем старую лицензию как свободную
				$IsUpdate = DB_Update('ISPswLicenses',Array('IsUsed'=>'no','IsInternal'=>'yes','Flag'=>''),Array('Where'=>"`IP` = '" . $ISPswOrder['IP'] . "' AND `ISPtype` = '" . $ISPswOrder['ISPtype'] . "'"));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
			}
		}
	}else{
		# если лицензия внешняя, проверяем новый IP, и меняем адресок
		if(!IspSoft_Check_ISPsystem_IP($Settings, $ISPswScheme))
		  return new gException('ISPsw_IP_ADDRESS_IN_USE','Для указанного IP адреса [' . $IP . '] уже есть лицензия такого типа. За более подробной информацией, обратитесь в службу поддержки пользователей.');
		#-----------------------------------------------------------------------
		$ISPswScheme['elid'] = $ISPswLicense['elid'];
		$IsCreate = IspSoft_Change_IP($Settings,$ISPswScheme);
		
	}
	#-----------------------------------------------------------------------
        switch(ValueOf($IsCreate)){
        case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('CANNOT_ORDER_LICENSE','При смене IP произошла непредвиденная ошибка. При повторении данного сообщения, обратитесь в службу поддержки пользователей, указав последовательность ваших действий.');
	case 'true':
		#-----------------------------------------------------------------------
		#-----------------------------------------------------------------------
		# меняем IP в заказе софта
		$IsUpdate = DB_Update('ISPswOrders',Array('IP'=>$IP),Array('ID'=>$ISPswOrderID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-----------------------------------------------------------------------
		#-----------------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Active','RowsIDs'=>$ISPswOrder['ID'],'Comment'=>"IP адрес лицензии успешно изменён [" . $ISPswOrder['IP'] . "->" . $IP . "]"));
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
					'Text'		=> SPrintF('IP адрес лицензии успешно изменён [%s->%s]',$ISPswOrder['IP'],$IP),
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-----------------------------------------------------------
			#-----------------------------------------------------------
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-----------------------------------------------------------
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
        	return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	return Array('Status'=>'Ok');
	#-----------------------------------------------------------------------
      default:
        return ERROR | @Trigger_Error(101);
	#-----------------------------------------------------------------------
    }
  default:
    return ERROR | @Trigger_Error(101);
    #-----------------------------------------------------------------------
}
#-------------------------------------------------------------------------------

?>
