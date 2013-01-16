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
if(Is_Error(System_Load('libs/IspSoft.php','libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# get config values
$Config = Config();
$Settings = $Config['IspSoft']['Settings'];
#-------------------------------------------------------------------------------
# достаём лицензии с сайта ispsystem
$Doc = IspSoft_Get_List_Licenses($Settings);
# проверяем, что вернулось
switch(ValueOf($Doc)){
case 'array':
	# all OK
	break;
default:
	return(time() + 3600);
}
#Debug("[comp/Tasks/ISPswCheckLicenses]: Doc = " . print_r($Doc, true));
# перебираем лицензии
foreach($Doc as $License){
	#Debug("[comp/Tasks/ISPswCheckLicenses]: " . print_r($License, true));
	#---------------------------------------------------------------
	$ExpireDate = StrToTime($License['expiredate']);
	$ExpireDate = MkTime(23, 59, 59, date("m", $ExpireDate), date("d", $ExpireDate), date("Y", $ExpireDate));
	#---------------------------------------------------------------
	$StatusID = "UnSeted";
	if($License['status'] == 1){$StatusID = "Waiting";}
	if($License['status'] == 2){$StatusID = "Active";}
	if($License['status'] == 3){$StatusID = "Suspended";}
	if($License['status'] == 4){$StatusID = "Deleted";}
	if($License['status'] == 5){$StatusID = "OnCreate";}
	#---------------------------------------------------------------
	# проверяем наличие лицензии с таким идентификатором в биллинге
	$ISPswLic = DB_Select('ISPswLicenses',Array('ID','elid','ip','StatusID'),Array('UNIQ','Where'=>'`elid`=' . $License['id']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ISPswLic)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	case 'exception':
		# нет такой лицензии в нашем биллинге
		Debug("[comp/Tasks/ISPswCheckLicenses]: not found license #" . $License['id']);
		$Event = Array(
				'UserID'	=> 100,
				'PriorityID'	=> 'Error',
				'Text'		=> SPrintF('Найдена неучтённая лицензия ISPsystem #%u, продукт (%s), период (%s), IP (%s)',$License['id'],$License['price'],$License['period'],$License['ip']),
				'IsReaded'	=> FALSE
			);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------
		# вносим лицензию в БД
		$IsInsert = DB_Insert(
				'ISPswLicenses',
				Array(
					'IP'		=> $License['ip'],
					'elid'		=> $License['id'],
					'IsInternal'	=> 'no',	# TODO надо бы по IP определять
					'IsUsed'	=> 'no',
					'ISPname'	=> (IsSet($License['licname'])?$License['licname']:'Имя не задано'),
					'StatusID'	=> $StatusID,
					'CreateDate'	=> time(),
					'UpdateDate'	=> time(),
					'ExpireDate'	=> $ExpireDate,
					'Flag'		=> 'Locked'
				)
			);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		break;
	#-------------------------------------------------------------------------------
	case 'array';
		# лицензия в биллинге есть
		Debug("[comp/Tasks/ISPswCheckLicenses]: found license #" . $License['id']);
		#-------------------------------------------------------------------------------
		# сравниваем IP адреса в биллинге ISPsystem и у нас
		if($License['ip'] != $ISPswLic['ip']){
			#-------------------------------------------------------------------------
			Debug(SPrintF("[comp/Tasks/ISPswCheckLicenses]: change license #%s IP %s->%s",$License['id'],$ISPswLic['ip'],$License['ip']));
			#-------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',
						Array(  'ModeID'        => 'ISPswLicenses',
							'IsNotNotify'   => TRUE,
							'IsNoTrigger'   => TRUE,
							'StatusID'      => $StatusID,
							'RowsIDs'       => $ISPswLic['ID'],
							'Comment'       => SPrintF('Изменение IP в биллинге ISPsystem [%s->%s]',$ISPswLic['ip'],$License['ip'])
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
		# обновляем данные лицензии
		$IsUpdate = DB_Update(
					'ISPswLicenses',
					Array(
						'IP'		=> $License['ip'],
						'ISPname'	=> (IsSet($License['licname'])?$License['licname']:'Имя не задано'),
						'UpdateDate'	=> time(),
						'ExpireDate'	=> $ExpireDate
					),
					Array(
						'Where'		=> "`elid` = " . $License['id']
					)
				);
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		# проверяем статус в биллинге, если отличается от статуса что получили - меняем статус штатно
		if($ISPswLic['StatusID'] != $StatusID){
			$Comp = Comp_Load('www/API/StatusSet',
						Array(  'ModeID'        => 'ISPswLicenses',
							'IsNotNotify'   => TRUE,
							'IsNoTrigger'   => TRUE,
							'StatusID'      => $StatusID,
							'RowsIDs'       => $ISPswLic['ID'],
							'Comment'       => 'Изменение статуса в биллинге ISPsystem [' . $ISPswLic['StatusID'] . '->' . $StatusID . ']'
						)
					);
			#-------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				Debug("[comp/Tasks/ISPswCheckLicenses]: changed status for license #" . $License['id'] . " " . $ISPswLic['StatusID'] . "->" . $StatusID);
				break;
			default:
				return ERROR | @Trigger_Error(101);

			}
		}
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}	# end foreach

# 
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
	$GLOBALS['TaskReturnInfo'] = SPrintF('Local licenses: %u',SizeOf($ISPswLicenses));
	#---------------------------------------------------------------------------
	foreach($ISPswLicenses as $ISPswLicense){
		$IsExists = false;
		#---------------------------------------------------------------------------
		foreach($Doc as $License){
			if($License['id'] == $ISPswLicense['elid']){
				Debug("[comp/Tasks/ISPswCheckLicenses]: license #" . $ISPswLicense['elid'] . " found in ISPmanager billing");
				$IsExists = true;
			}
		}
		if(!$IsExists){
			Debug("[comp/Tasks/ISPswCheckLicenses]: license #" . $ISPswLicense['elid'] . " not found in ISPmanager billing");
			$Event = Array(
					'UserID'	=> 100,
					'PriorityID'	=> 'Error',
					'Text'		=> "Найдена лицензия на ПО, отсутствующая в биллинге ISPsystem #" . $ISPswLicense['elid'],
					'IsReaded'	=> FALSE
				);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
		}
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}	# end of VPSSchemes


# помечаем в локальной базе как свободные те которые уже больше 30 дней свободны (или 31?)


return MkTime(4,35,0,Date('n'),Date('j')+1,Date('Y'));

#-------------------------------------------------------------------------------

?>
