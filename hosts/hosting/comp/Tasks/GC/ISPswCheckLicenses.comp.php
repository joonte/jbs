<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/BillManager.php','libs/HTTP.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# get config values
$Config = Config();
#-------------------------------------------------------------------------------
if(!$Config['Tasks']['Types']['GC']['ISPswCheckLicensesSettings'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByService(51000);
#-------------------------------------------------------------------------------
if(!Is_Array($Settings)){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: no configured servers for ISPsw service'));
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём лицензии с сайта ispsystem
$Doc = BillManager_Get_List_Licenses($Settings);
# проверяем, что вернулось
switch(ValueOf($Doc)){
case 'array':
	# all OK
	break;
default:
	return TRUE;
}
#-------------------------------------------------------------------------------
#Debug("[comp/Tasks/ISPswCheckLicenses]: Doc = " . print_r($Doc, true));
# перебираем лицензии
foreach($Doc as $License){
	#-------------------------------------------------------------------------------
	#Debug(SPrintF("[comp/Tasks/ISPswCheckLicenses]: License = %s",print_r($License, true)));
	#-------------------------------------------------------------------------------
	if(!IsSet($License['expiredate']))
		continue;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# достаём addon этого софта
	$Comp = Comp_Load('Formats/ISPswOrder/SoftWareList',TRUE,$License['pricelist_id'],TRUE,TRUE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: IP = %s; elid = %u; addon = %s',$License['ip'],$License['id'],$Comp));
	$addon = 1;
	#-------------------------------------------------------------------------------
	if($Comp)
		if(IsSet($License[$Comp]))
			$addon = $License[$Comp];
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: addon name = %s; addon value = %s',$Comp,$addon));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$ExpireDate = StrToTime($License['expiredate']);
	$ExpireDate = MkTime(23, 59, 59, date("m", $ExpireDate), date("d", $ExpireDate), date("Y", $ExpireDate));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Statuses = $Config['Statuses']['ISPswLicenses'];
	#-------------------------------------------------------------------------------
	$StatusID = "UnSeted";
	#-------------------------------------------------------------------------------
	#Debug(SPrintF("[comp/Tasks/ISPswCheckLicenses]: Statuses = %s",print_r($Statuses, true)));
	foreach(Array_Keys($Statuses) as $Status)
		if($Statuses[$Status]['status'] == $License['status'])
			$StatusID = $Status;
        #-------------------------------------------------------------------------------
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/ISPswCheckLicenses'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# проверяем наличие лицензии с таким идентификатором в биллинге
	$ISPswLic = DB_Select('ISPswLicenses',Array('ID','elid','ip','StatusID'),Array('UNIQ','Where'=>SPrintF('`elid`=%u',$License['id'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ISPswLic)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	case 'exception':
		#-------------------------------------------------------------------------------
		# нет такой лицензии в нашем биллинге
		Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: not found license #%s',$License['id']));
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> 100,
				'PriorityID'	=> 'Error',
				'Text'		=> SPrintF('Найдена неучтённая лицензия ISPsystem #%u, продукт (%s), период (%s), IP (%s)',$License['id'],$License['pricelist_id'],$License['period'],$License['ip']),
				'IsReaded'	=> FALSE
			);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# вносим лицензию в БД
		$IsInsert = DB_Insert(
				'ISPswLicenses',
				Array(
					'pricelist_id'		=> $License['pricelist_id'],
					'period'		=> $License['period'],
					'addon'			=> $addon,
					'IP'			=> $License['ip'],
					'remoteip'		=> (IsSet($License['remoteip'])?$License['remoteip']:''),
					'elid'			=> $License['id'],
					'LicKey'		=> $License['lickey'],
					'IsInternal'		=> 'no',	# TODO надо бы по IP определять
					'IsUsed'		=> 'no',
					'ISPname'		=> (IsSet($License['licname'])?$License['licname']:'Имя не задано'),
					'StatusID'		=> $StatusID,
					'CreateDate'		=> Time(),
					'ip_change_date'	=> StrToTime($License['ip_change_date']),
					'lickey_change_date'	=> StrToTime($License['lickey_change_date']),
					'update_expiredate'	=> StrToTime($License['update_expiredate']),
					'StatusDate'		=> Time(),
					'ExpireDate'		=> $ExpireDate,
					'Flag'			=> 'Locked'
				)
			);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array';
		#-------------------------------------------------------------------------------
		# лицензия в биллинге есть
		Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: found license #%u',$License['id']));
		#-------------------------------------------------------------------------------
		if(StrLen($License['lickey']) < 2 && $License['pricelist_id'] > 1000){
			#-------------------------------------------------------------------------------
			# у лицензии нет ключа. Вешаем ахтунг...
			$Event = Array(
					'UserID'	=> 100,
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Найдена лицензия ISPsystem 5 версии без ключа: #%u, продукт (%s), период (%s), IP (%s)',$License['id'],$License['pricelist_id'],$License['period'],$License['ip']),
					'IsReaded'	=> FALSE
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# сравниваем IP адреса в биллинге ISPsystem и у нас
		if($License['ip'] != $ISPswLic['ip']){
			#-------------------------------------------------------------------------
			Debug(SPrintF("[comp/Tasks/ISPswCheckLicenses]: change license #%s IP %s->%s",$License['id'],$ISPswLic['ip'],$License['ip']));
			#-------------------------------------------------------------------------
			$Comp = Comp_Load(
					'www/API/StatusSet',
					Array(
						'ModeID'        => 'ISPswLicenses',
						'IsNotNotify'   => TRUE,
						'IsNoTrigger'   => TRUE,
						'StatusID'      => $StatusID,
						'RowsIDs'       => $ISPswLic['ID'],
						'Comment'       => SPrintF('ISPsystem IP: %s->%s',$ISPswLic['ip'],$License['ip'])
						)
					);
			#-------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);

			}
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# обновляем данные лицензии
		$IsUpdate = DB_Update(
					'ISPswLicenses',
					Array(
						'LicKey'		=> $License['lickey'],
						'pricelist_id'  	=> $License['pricelist_id'],
						'period'        	=> $License['period'],
						'addon'			=> $addon,
						'IP'			=> $License['ip'],
						'remoteip'		=> (IsSet($License['remoteip'])?$License['remoteip']:''),
						'ISPname'		=> (IsSet($License['licname'])?$License['licname']:'Имя не задано'),
						'ip_change_date'	=> StrToTime($License['ip_change_date']),
						'lickey_change_date'	=> StrToTime($License['lickey_change_date']),
						'update_expiredate'	=> StrToTime($License['update_expiredate']),
						'ExpireDate'		=> $ExpireDate
					),
					Array(
						'Where'			=> SPrintF('`elid` = %u',$License['id'])
					)
				);
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# проверяем статус в биллинге, если отличается от статуса что получили - меняем статус штатно
		if($ISPswLic['StatusID'] != $StatusID){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',
						Array(  'ModeID'        => 'ISPswLicenses',
							'IsNotNotify'   => TRUE,
							'IsNoTrigger'   => TRUE,
							'StatusID'      => $StatusID,
							'RowsIDs'       => $ISPswLic['ID'],
							'Comment'       => SPrintF('ISPsystem Status: %s->%s',$ISPswLic['StatusID'],$StatusID)
						)
					);
			#-------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: changed status for license #%u %s->%s',$License['id'],$ISPswLic['StatusID'],$StatusID));
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);

			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}	# end foreach

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем локальные, находим те которых нет в испсистем - вешаем уведомления об этом
$ISPswLicenses = DB_Select('ISPswLicenses',Array('ID','elid'));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswLicenses)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	if(IsSet($GLOBALS['TaskReturnInfo']))
		$GLOBALS['TaskReturnInfo'] = Array($GLOBALS['TaskReturnInfo'],SPrintF('Local licenses: %u',SizeOf($ISPswLicenses)));
	#---------------------------------------------------------------------------
	foreach($ISPswLicenses as $ISPswLicense){
		#-------------------------------------------------------------------------------
		$IsExists = false;
		#---------------------------------------------------------------------------
		foreach($Doc as $License){
			#-------------------------------------------------------------------------------
			if(!IsSet($License['expiredate']))
				continue;
			#-------------------------------------------------------------------------------
			if($License['id'] == $ISPswLicense['elid']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: license #%u found in ISPsystem billing',$ISPswLicense['elid']));
				#-------------------------------------------------------------------------------
				$IsExists = true;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(!$IsExists){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/ISPswCheckLicenses]: license #%u not found in ISPsystem billing',$ISPswLicense['elid']));
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> 100,
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('В локальной БД обнаружена лицензия отсутствующая в биллинге ISPsystem #%u',$ISPswLicense['elid']),
					'IsReaded'	=> FALSE
				);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}	# end of VPSSchemes
#-------------------------------------------------------------------------------

# помечаем в локальной базе как свободные те которые уже больше 30 дней свободны (или 31?)


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# обновляем IP адреса заказов лицензий, у которых есть сслыка на elid
$ISPswOrders = DB_Select('ISPswOrdersOwners',Array('ID','OrderID','IP','LicenseID'),Array('Where'=>'`StatusID` = "Active"'));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/ISPswCheckLicenses'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach($ISPswOrders as $ISPswOrder){
		#-------------------------------------------------------------------------------
		if($ISPswOrder['LicenseID']){
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# выбираем данные лицензии на которую ссылается заказ
			$License = DB_Select('ISPswLicensesOwners',Array('ID','IP','StatusID'),Array('UNIQ','ID'=>$ISPswOrder['LicenseID']));
			#-------------------------------------------------------------------------------
			switch(ValueOf($License)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				# заказ ссылается на несуществующую лицензию. вывешиваем ахтунг
				$Event = Array(
						'UserID'	=> 100,
						'PriorityID'	=> 'Error',
						'Text'		=> SPrintF('Заказ ПО #%s ссылается на несуществующую лицензию #%s',$ISPswOrder['OrderID'],$ISPswOrder['LicenseID']),
						'IsReaded'	=> FALSE
						);
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				#-------------------------------------------------------------------------------
				if($License['IP'] != $ISPswOrder['IP']){
					#-------------------------------------------------------------------------------
					$IsUpdate = DB_Update('ISPswOrders',Array('IP'=>$License['IP']),Array('ID'=>$ISPswOrder['ID']));
					if(Is_Error($IsUpdate))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			# заказ без лицензии. вывешиваем ахтунг
			$Event = Array(
					'UserID'	=> 100,
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Обнаружен заказ ПО #%s без указания лицензии',$ISPswOrder['OrderID']),
					'IsReaded'	=> FALSE
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
