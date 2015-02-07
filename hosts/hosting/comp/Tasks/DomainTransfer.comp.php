<?php

#-------------------------------------------------------------------------------
/** @author Serge Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DomainOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/WhoIs.php','classes/Registrator.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('UserID','DomainName','AuthInfo','ProfileID','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`','ServerID','StatusID','Ns1Name','Ns1IP','Ns2Name','Ns2IP','Ns3Name','Ns3IP','Ns4Name','Ns4IP','IsPrivateWhoIs','PersonID');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	#return ERROR | @Trigger_Error(400);
	# к моменту выполнения задания, бывает что юзер уже успел грохнуть заказ...
	Debug("[Task/DomainTransfer]: Заказа на домен уже не существует, вероятно пользователь его удалил");
	return TRUE;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
	#-------------------------------------------------------------------------------
	$WhoIs = WhoIs_Check($DomainOrder['DomainName'],$DomainOrder['DomainZone']);
	#-------------------------------------------------------------------------------
	$Registrar = IsSet($WhoIs['Registrar'])?$WhoIs['Registrar']:'NOT_FOUND';
	#-------------------------------------------------------------------------------
	$Server = DB_Select('Servers','Params',Array('UNIQ','ID'=>$DomainOrder['ServerID']));
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[Task/DomainTransfer]: Registrar: %s; PrefixNic: %s',$Registrar,$Server['Params']['PrefixNic']));
	#-------------------------------------------------------------------------------
	$IsInternal = (Preg_Match(SPrintF('/%s/',$Server['Params']['PrefixNic']),$Registrar))?TRUE:FALSE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Server = new Registrator();
	#-------------------------------------------------------------------------------
	$IsSelected = $Server->Select((integer)$DomainOrder['ServerID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
	case 'true':
		#-------------------------------------------------------------------------------
		switch($DomainOrder['StatusID']){
		case 'OnTransfer':
			#-------------------------------------------------------------------------------
			# TODO у reg.ru такие переносы делаются отдельным таском, у webnames такого не реализовано
			if($IsInternal){
				#-------------------------------------------------------------------------------
				Debug("[Task/DomainTransfer]: IsInternal: TRUE");
				return TRUE;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Params = Array('AuthInfo' => $DomainOrder['AuthInfo']);
			#-------------------------------------------------------------------------------
			if($DomainOrder['PersonID']){
				#-------------------------------------------------------------------------------
				$Params['PersonID']	= $DomainOrder['PersonID'];
				$Params['Person']	= Array();
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				if(!In_Array($DomainOrder['DomainZone'],Array('ru','su','рф'))){
					#-------------------------------------------------------------------------------
					$ProfileID = $DomainOrder['ProfileID'];
					#-------------------------------------------------------------------------------
					$Profile = DB_Select('Profiles',Array('TemplateID','Attribs'),Array('UNIQ','ID'=>$ProfileID));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Profile)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
						# готовим поля профиля
						$ProfileCompile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
						#-------------------------------------------------------------------------------
						switch(ValueOf($ProfileCompile)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------------
							# страна должна быть кодом
							if(IsSet($Profile['Attribs']['pCountry'])){$ProfileCompile['Attribs']['pCountry'] = $Profile['Attribs']['pCountry'];}
							if(IsSet($Profile['Attribs']['PasportCountry'])){$ProfileCompile['Attribs']['PasportCountry'] = $Profile['Attribs']['PasportCountry'];}
							if(IsSet($Profile['Attribs']['jCountry'])){$ProfileCompile['Attribs']['jCountry'] = $Profile['Attribs']['jCountry'];}
							#-------------------------------------------------------------------------------
							break;
							#-------------------------------------------------------------------------------
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------------
						break;
						#-------------------------------------------------------------------------------
						default:
					return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
					$Params['PersonID']	= $Profile['TemplateID'];
					$Params['Person']	= $ProfileCompile['Attribs'];
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Params['DomainName']		= $DomainOrder['DomainName'];
			$Params['DomainZone']		= $DomainOrder['DomainZone'];
			$Params['Ns1Name']		= $DomainOrder['Ns1Name'];
			$Params['Ns2Name']		= $DomainOrder['Ns2Name'];
			$Params['Ns3Name']		= $DomainOrder['Ns3Name'];
			$Params['Ns4Name']		= $DomainOrder['Ns4Name'];
			$Params['Ns1IP']		= $DomainOrder['Ns1IP'];
			$Params['Ns2IP']		= $DomainOrder['Ns2IP'];
			$Params['Ns3IP']		= $DomainOrder['Ns3IP'];
			$Params['Ns4IP']		= $DomainOrder['Ns4IP'];
			$Params['ContractID']		= '';
			$Params['IsPrivateWhoIs']	= $DomainOrder['IsPrivateWhoIs'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$IsDomainTransfer = $Server->DomainTransfer($DomainOrder['DomainName'],$DomainOrder['DomainZone'],$Params);
			#-------------------------------------------------------------------------------
			switch(ValueOf($IsDomainTransfer)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
			case 'array':
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('DomainOrders',Array('ProfileID'=>NULL,'DomainID'=>$IsDomainTransfer['DomainID']),Array('ID'=>$DomainOrderID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Event = Array(
						'UserID'	=> $DomainOrder['UserID'],
						'PriorityID'    => 'Hosting',
						'Text'          => SPrintF('Подана заявка на перенос домена (%s.%s)',$DomainOrder['DomainName'],$DomainOrder['DomainZone']),
						'IsReaded'      => TRUE
						);
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				return TRUE;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		default:
			return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
		}
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
