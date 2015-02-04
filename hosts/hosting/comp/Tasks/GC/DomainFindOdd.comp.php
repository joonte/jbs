<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Registrator.class.php','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address','Params'),Array('Where'=>Array('`IsActive` = "yes"','(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 20000')));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	# No more...
	Debug('[comp/Tasks/GC/DomainFindOddg]: Регистраторы не найдены');
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
foreach($Servers as $NowReg){
	#-----------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'][] = $NowReg['Params']['Name'];
	#-------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/GC/DomainFindOdd]: Поиск лишних доменов у %s (ID %d, тип %s)',$NowReg['Params']['Name'],$NowReg['ID'],$NowReg['Params']['SystemID']));
	#-------------------------------------------------------------------
	$Server = new Registrator();
	#-------------------------------------------------------------------
	$IsSelected = $Server->Select((integer)$NowReg['ID']);
	#-------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
	case 'true':
		#-------------------------------------------------------------------------------
		# реализация JBS-805
		$Accept = $Server->DomainsAccept();
		#return TRUE;
		#-------------------------------------------------------------------------------
		$RegDomains = $Server->GetListDomains();
		#-------------------------------------------------------------------------------
		break;
	default:
		return new gException('WRONG_STATUS','Регистратор не определён');
	}
	#-----------------------------------------------------------------------
	switch(ValueOf($RegDomains)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#---------------------------------------------------------------
		switch($RegDomains->CodeID){
		case 'REGISTRATOR_ERROR':
			Debug(SPrintF('[comp/Tasks/GC/DomainFindOdd]: %s: %s',$NowReg['Params']['Name'],$RegDomains->String));
			break;
		default:
			Debug(SPrintF('[comp/Tasks/GC/DomainFindOdd]: Для регистратора %s (ID %d, тип %s) поиск лишних доменов не реализован.',$NowReg['Params']['Name'],$NowReg['ID'],$NowReg['Params']['SystemID']));
		}
		#---------------------------------------------------------------
		break;
		#---------------------------------------------------------------
	case 'array':
		#---------------------------------------------------------------
		if(!$RegDomains['Status']){
			Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: У регистратора %s не найдено доменов',$NowReg['Params']['Name']));
			break;
		}
		#---------------------------------------------------------------
		# достаём список доменов этого регистратора у нас в биллинге
		$Where = Array(
				'`DomainSchemes`.`ID` = `SchemeID`',
				'`StatusID` = "Active" OR `StatusID` = "Suspended"',
				SPrintF('`DomainOrdersOwners`.`ServerID` = %u',$NowReg['ID'])
				);
		$Domains = DB_Select(
					Array('DomainOrdersOwners','DomainSchemes'),
					Array('CONCAT(`DomainOrdersOwners`.`DomainName`,".",`DomainSchemes`.`Name`) AS `Domain`'),
					Array('Where'=>$Where,'GroupBy'=>'Domain','SortOn'=>'Domain')
					);
		switch(ValueOf($Domains)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			# No more...
			Debug("[comp/Tasks/GC/DomainFindOddg]: Нет доменов");
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			# строим массив доменов из биллинга
			$BillDomains = Array();
			#-------------------------------------------------------------------------------
			foreach ($Domains as $Domain)
				$BillDomains[] = Mb_StrToLower($Domain['Domain'],'UTF-8');
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: [%s] доменов у регистратора %s, в биллинге %s',$NowReg['Params']['Name'],SizeOf($RegDomains['Domains']),SizeOf($BillDomains)));
			#-------------------------------------------------------------------------------
			# сортируем массивы
			ASort($RegDomains['Domains']);
			ASort($BillDomains);
			#-------------------------------------------------------------------------------
			# лишние у регистратора
			$DomainsOdd = Array_Diff($RegDomains['Domains'],$BillDomains);
			#-------------------------------------------------------------------------------
			if(SizeOf($DomainsOdd) > 0){
				#-------------------------------------------------------------------------------
				foreach($DomainsOdd as $DomainOdd){
					#-------------------------------------------------------------------------------
					# ищщем этот домен в биллинге, безотносительно его статуса, но у того же регистратора
					$Where = Array(
							SPrintF('CONCAT(`DomainOrdersOwners`.`DomainName`,".",`DomainSchemes`.`Name`) = "%s"',$DomainOdd),
							SPrintF('`DomainOrdersOwners`.`ServerID` = %u',$NowReg['ID']),
							'`DomainSchemes`.`ID` = `SchemeID`'
							);
					$Count = DB_Count(Array('DomainOrdersOwners','DomainSchemes'),Array('Where'=>$Where));
					if(Is_Error($Count))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					if(!$Count){
						#-------------------------------------------------------------------------------
						$Parse = WhoIs_Parse($DomainOdd);
						#-------------------------------------------------------------------------------
						switch(ValueOf($Parse)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'false':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------------
							$IsCheck = WhoIs_Check($DomainName = $Parse['DomainName'],$DomainZone = $Parse['DomainZone']);
							#-------------------------------------------------------------------------------
							switch(ValueOf($IsCheck)){
							case 'error':
								return ERROR | @Trigger_Error(500);
							case 'false':
								break;
							case 'array':
								#-------------------------------------------------------------------------------
								# если он кончился - не обращаем внимания
								if($IsCheck['ExpirationDate'] < Time())
									continue 3;
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
						$Message = SPrintF('У регистратора %s найден лишний домен %s',$NowReg['Params']['Name'],$DomainOdd);
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: %s',$Message));
						#-------------------------------------------------------------------------------
						$Event = Array('Text' => $Message,'PriorityID' => 'Error','IsReaded' => FALSE);
						$Event = Comp_Load('Events/EventInsert', $Event);
						if(!$Event)
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
					}else{
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: Домен %s/%s, в биллинге есть, но его статус не соответствует критериям выборки',$DomainOdd,$NowReg['Params']['Name']));
						#-------------------------------------------------------------------------------
						# JBS-595 - проверяем не на переносе ли он - возможно перенеос завершилсяa
						$Columns = Array('`DomainOrdersOwners`.`ID` AS `ID`','StatusID','ExpirationDate','`DomainOrdersOwners`.`UserID` AS `UserID`');
						#-------------------------------------------------------------------------------
						$IsTransfer = DB_Select(Array('DomainOrdersOwners','DomainSchemes'),$Columns,Array('UNIQ','Where'=>$Where));
						#-------------------------------------------------------------------------------
						switch(ValueOf($IsTransfer)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: Домен %s, регистратор не соответсвует %s',$DomainOdd,$NowReg['Params']['Name']));
							break;
						case 'array':
							#-------------------------------------------------------------------------------
							$Comp = Comp_Load('www/Administrator/API/DomainOrderWhoIsUpdate',Array('DomainOrderID'=>$IsTransfer['ID']));
							if(Is_Error($Comp))
								return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------------
							if(In_Array($IsTransfer['StatusID'],Array('ForTransfer','OnTransfer')) || ($IsTransfer['StatusID'] == 'Deleted' && $IsTransfer['ExpirationDate'] > Time() + 90 * 24 * 3600)){
								#-------------------------------------------------------------------------------
								if($IsTransfer['StatusID'] == 'Deleted'){
									#-------------------------------------------------------------------------------
									$Message = SPrintF('Домен %s, регистратор %s, продлён без использования биллинга',$DomainOdd,$NowReg['Params']['Name']);
									#-------------------------------------------------------------------------------
								}else{
									#-------------------------------------------------------------------------------
									$Message = SPrintF('Домен %s перенесён к регистратору %s',$DomainOdd,$NowReg['Params']['Name']);
									#-------------------------------------------------------------------------------
								}
								#-------------------------------------------------------------------------------
								Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: %s',$Message));
								#-------------------------------------------------------------------------------
								# TODO подправляем регистратора, т.к. у меня первый же перенос - задание на перенос
								# одному регистратору, а домен перенесли к другому...
								#$IsUpdate = DB_Update('DomainOrders',Array('SchemeID'=>'надо достать тариф?'),Array('ID'=>$IsTransfer['ID']));
								#if(Is_Error($IsUpdate))
								#	return ERROR | @Trigger_Error(500);
								#-------------------------------------------------------------------------------
								# ставим статус "Активен"
								$Comp = Comp_Load(
										'www/API/StatusSet',
										Array(
											'ModeID'        => 'DomainOrders',
											'StatusID'      => 'Active',
											'RowsIDs'       => $IsTransfer['ID'],
											'Comment'       => $Message
											)
										);
								#-------------------------------------------------------------------------------
								switch(ValueOf($Comp)){
								case 'error':
									return ERROR | @Trigger_Error(500);
								case 'exception':
									return ERROR | @Trigger_Error(400);
								case 'array':
									#-------------------------------------------------------------------------------
									$Event = Array('Text'=>$Message,'PriorityID'=>'Notice','IsReaded'=>FALSE,'UserID'=>$IsTransfer['UserID']);
									#-------------------------------------------------------------------------------
									$Event = Comp_Load('Events/EventInsert', $Event);
									if(!$Event)
										return ERROR | @Trigger_Error(500);
									#-------------------------------------------------------------------------------
									break;
									#-------------------------------------------------------------------------------
								default:
									return ERROR | @Trigger_Error(101);
								}
								#-------------------------------------------------------------------------------
							}else{
								#-------------------------------------------------------------------------------
								Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: Домен %s ещё не удалён у регистратора',$DomainOdd));
								#-------------------------------------------------------------------------------
							}
							#-------------------------------------------------------------------------------
							break;
							#-------------------------------------------------------------------------------
						default:
							return ERROR | @Trigger_Error(101);
						}
					}
				}
			}
			#-------------------------------------------------------------------------------
			# лишние в биллинге
			$DomainsOdd = Array_Diff($BillDomains,$RegDomains['Domains']);
			#-------------------------------------------------------------------------------
			if(SizeOf($DomainsOdd) > 0){
				#-------------------------------------------------------------------------------
				foreach($DomainsOdd as $DomainOdd){
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/Tasks/GC/DomainFindOddg]: Найден домен %s отсутствующий у регистратора %s',$DomainOdd,$NowReg['Params']['Name']));
					#-------------------------------------------------------------------------------
					# Ищщем параметры этого заказа на домен
					$Where = Array(
							'`DomainSchemes`.`ID` = `SchemeID`',
							SPrintF("CONCAT(`DomainOrdersOwners`.`DomainName`,'.',`DomainSchemes`.`Name`) = '%s'",$DomainOdd),
							SPrintF('`DomainOrdersOwners`.`ServerID` = %u',$NowReg['ID'])
							);
					$DomainOrder = DB_Select(Array('DomainOrdersOwners','DomainSchemes'),Array('StatusID','StatusDate','`DomainOrdersOwners`.`ID` AS `ID`','`DomainOrdersOwners`.`UserID` AS `UserID`'),Array('UNIQ','Where'=>$Where));
					#-------------------------------------------------------------------------------
					switch(ValueOf($DomainOrder)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
						# если от статуса менее суток - пропускаем, были накладки,
						# когда в 4 час ночи зарегал, а в пять его удалило, т.к. не найден =)
						if($DomainOrder['StatusDate'] + 24*3600 > Time())
							continue;
						#-------------------------------------------------------------------------------
						break;
						#-------------------------------------------------------------------------------
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
					# ставим статус "Удалён"
					$Comp = Comp_Load(
							'www/API/StatusSet',
							Array(
								'ModeID'	=> 'DomainOrders',
								'StatusID'	=> 'Deleted',
								'RowsIDs'	=> $DomainOrder['ID'],
								'Comment'	=> SPrintF('Заказ домена не найден у регистратора %s',$NowReg['Params']['Name'])
								)
							);
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
						if($DomainOrder['StatusID'] != 'Suspended'){
							#-------------------------------------------------------------------------------
							$Event = Array(
									'UserID'	=> $DomainOrder['UserID'],
									'PriorityID'	=> 'Error',
									'Text'		=> SPrintF('Заказ домена %s не найден у регистратора %s. Статус заказа изменен на "Удален".',$DomainOdd,$NowReg['Params']['Name']),
									'IsReaded'	=> FALSE
									);
							#-------------------------------------------------------------------------------
							$Event = Comp_Load('Events/EventInsert',$Event);
							if(!$Event)
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
		break;
		#-------------------------------------------------------------------------------
	default:
		return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
