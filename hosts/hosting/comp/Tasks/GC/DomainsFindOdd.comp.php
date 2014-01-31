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
$Registrators = DB_Select('Registrators',Array('ID','Name','TypeID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Registrators)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	Debug("[comp/Tasks/GC/DomainsFindOdd]: Регистраторы не найдены");
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
foreach($Registrators as $NowReg){
	#-----------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'][] = $NowReg['Name'];
	#-------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Поиск лишних доменов у %s (ID %d, тип %s)',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
	#-------------------------------------------------------------------
	$Registrator = new Registrator();
	#-------------------------------------------------------------------
	$IsSelected = $Registrator->Select((integer)$NowReg['ID']);
	#-------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
	case 'true':
		#-----------------------------------------------------------
		$RegDomains = $Registrator->GetListDomains();
		#-----------------------------------------------------------
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
			Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: %s: %s',$NowReg['Name'],$RegDomains->String));
			break;
		default:
			Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Для регистратора %s (ID %d, тип %s) поиск лишних доменов не реализован.',$NowReg['Name'],$NowReg['ID'],$NowReg['TypeID']));
		}
		#---------------------------------------------------------------
		break;
		#---------------------------------------------------------------
	case 'array':
		#---------------------------------------------------------------
		if(!$RegDomains['Status']){
			Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: У регистратора %s не найдено доменов',$NowReg['Name']));
			break;
		}
		#---------------------------------------------------------------
		# достаём список доменов этого регистратора у нас в биллинге
		$Where = Array(
				'`DomainsSchemes`.`ID` = `SchemeID`',
				SPrintF('`DomainsSchemes`.`RegistratorID` = %u',$NowReg['ID']),
				'`StatusID` = "Active" OR `StatusID` = "Suspended"'
				);
		$Domains = DB_Select(
					Array('DomainsOrdersOwners','DomainsSchemes'),
					Array('CONCAT(`DomainsOrdersOwners`.`DomainName`,".",`DomainsSchemes`.`Name`) AS `Domain`'),
					Array('Where'=>$Where,'GroupBy'=>'Domain','SortOn'=>'Domain')
					);
		switch(ValueOf($Domains)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			# No more...
			Debug("[comp/Tasks/GC/DomainsFindOdd]: Нет доменов");
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			# строим массив доменов из биллинга
			$BillDomains = Array();
			#-----------------------------------------------------------
			foreach ($Domains as $Domain)
				$BillDomains[] = Mb_StrToLower($Domain['Domain'],'UTF-8');
			#-----------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: [%s] доменов у регистратора %s, в биллинге %s',$NowReg['Name'],SizeOf($RegDomains['Domains']),SizeOf($BillDomains)));
			#-----------------------------------------------------------
			# сортируем массивы
			ASort($RegDomains['Domains']);
			ASort($BillDomains);
			#-----------------------------------------------------------
			# лишние у регистратора
			$DomainsOdd = Array_Diff($RegDomains['Domains'],$BillDomains);
			if(SizeOf($DomainsOdd) > 0){
				foreach($DomainsOdd as $DomainOdd){
					# ищщем этот домен в биллинге, безотносительно его статуса, но у того же регистратора
					$Where = Array(
							SPrintF("CONCAT(`DomainsOrdersOwners`.`DomainName`,'.',`DomainsSchemes`.`Name`) = '%s'",$DomainOdd),
							'`DomainsSchemes`.`ID` = `SchemeID`',
							SPrintF('`DomainsSchemes`.`RegistratorID` = %u',$NowReg['ID'])
							);
					$Count = DB_Count(Array('DomainsOrdersOwners','DomainsSchemes'),Array('Where'=>$Where));
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
								if($ExpirationDate < Time())
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
						$Message = SPrintF('У регистратора %s найден лишний домен %s',$NowReg['Name'],$DomainOdd);
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: %s',$Message));
						#-------------------------------------------------------------------------------
						$Event = Array('Text' => $Message,'PriorityID' => 'Error','IsReaded' => FALSE);
						$Event = Comp_Load('Events/EventInsert', $Event);
						if(!$Event)
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
					}else{
						#-------------------------------------------------------------------------------
						Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Домен %s/%s, в биллинге есть, но его статус не соответствует критериям выборки',$DomainOdd,$NowReg['Name']));
						#-------------------------------------------------------------------------------
						# JBS-595 - проверяем не на переносе ли он - возможно перенеос завершился успешноa
						$Columns = Array('`DomainsOrdersOwners`.`ID` AS `ID`','StatusID','ExpirationDate','`DomainsOrdersOwners`.`UserID` AS `UserID`');
						#-------------------------------------------------------------------------------
						$IsTransfer = DB_Select(Array('DomainsOrdersOwners','DomainsSchemes'),$Columns,Array('UNIQ','Where'=>$Where));
						#-------------------------------------------------------------------------------
						switch(ValueOf($IsTransfer)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Домен %s, регистратор не соответсвует %s',$DomainOdd,$NowReg['Name']));
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
									$Message = SPrintF('Домен %s, регистратор %s, продлён без использования биллинга',$DomainOdd,$NowReg['Name']);
									#-------------------------------------------------------------------------------
								}else{
									#-------------------------------------------------------------------------------
									$Message = SPrintF('Домен %s успешно перенесён к регистратору %s',$DomainOdd,$NowReg['Name']);
									#-------------------------------------------------------------------------------
								}
								#-------------------------------------------------------------------------------
								Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: %s',$Message));
								#-------------------------------------------------------------------------------
								# TODO подправляем регистратора, т.к. у меня первый же перенос - задание на перенос
								# одному регистратору, а домен перенесли к другому...
								#$IsUpdate = DB_Update('DomainsOrders',Array('SchemeID'=>'надо достать тариф?'),Array('ID'=>$IsTransfer['ID']));
								#if(Is_Error($IsUpdate))
								#	return ERROR | @Trigger_Error(500);
								#-------------------------------------------------------------------------------
								# ставим статус "Активен"
								$Comp = Comp_Load(
										'www/API/StatusSet',
										Array(
											'ModeID'        => 'DomainsOrders',
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
								Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Домен %s ещё не удалён у регистратора',$DomainOdd));
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
			if(SizeOf($DomainsOdd) > 0){
				foreach($DomainsOdd as $DomainOdd){
					Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: Найден домен %s отсутствующий у регистратора %s',$DomainOdd,$NowReg['Name']));
					#-----------------------------------------------------------
					# Ищщем параметры этого заказа на домен
					$Where = Array(
							SPrintF("CONCAT(`DomainsOrdersOwners`.`DomainName`,'.',`DomainsSchemes`.`Name`) = '%s'",$DomainOdd),
							'`DomainsSchemes`.`ID` = `SchemeID`',
							SPrintF('`DomainsSchemes`.`RegistratorID` = %u',$NowReg['ID'])
							);
					$DomainOrder = DB_Select(Array('DomainsOrdersOwners','DomainsSchemes'),Array('StatusID','StatusDate','`DomainsOrdersOwners`.`ID` AS `ID`','`DomainsOrdersOwners`.`UserID` AS `UserID`'),Array('UNIQ','Where'=>$Where));
					#-----------------------------------------------------------
					switch(ValueOf($DomainOrder)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						# если от статуса менее суток - пропускаем, были накладки,
						# когда в 4 час ночи зарегал, а в пять его удалило, т.к. не найден =)
						if($DomainOrder['StatusDate'] + 24*3600 > Time())
							continue;
						#-----------------------------------------------------------
						break;
						#-----------------------------------------------------------
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-----------------------------------------------------------
					# ставим статус "Удалён"
					$Comp = Comp_Load(
							'www/API/StatusSet',
							Array(
								'ModeID'	=> 'DomainsOrders',
								'StatusID'	=> 'Deleted',
								'RowsIDs'	=> $DomainOrder['ID'],
								'Comment'	=> SPrintF('Заказ домена не найден у регистратора %s',$NowReg['Name'])
								)
							);
					#-----------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-----------------------------------------------------------
						if($DomainOrder['StatusID'] != 'Suspended'){
							#-----------------------------------------------------------
							$Event = Array(
									'UserID'	=> $DomainOrder['UserID'],
									'PriorityID'	=> 'Error',
									'Text'		=> SPrintF('Заказ домена %s не найден у регистратора %s. Статус заказа изменен на "Удален".',$DomainOdd,$NowReg['Name']),
									'IsReaded'	=> FALSE
									);
							#-----------------------------------------------------------
							$Event = Comp_Load('Events/EventInsert',$Event);
							if(!$Event)
								return ERROR | @Trigger_Error(500);
							#-----------------------------------------------------------
						}
						#-----------------------------------------------------------
						break;
						#-----------------------------------------------------------
					default:
						return ERROR | @Trigger_Error(101);
					}
				}
			}
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-----------------------------------------------------------
		break;
	default:
		return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
	}
	#-------------------------------------------------------------------
}
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
return TRUE;

?>
