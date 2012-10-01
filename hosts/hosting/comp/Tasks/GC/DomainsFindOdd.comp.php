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
if(Is_Error(System_Load('classes/Registrator.class.php')))
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
		#-----------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array();
		#-----------------------------------------------------------------------
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
				case 'array':
					if($RegDomains['Status']){
						#-----------------------------------------------------------
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
							# No more...
							Debug("[comp/Tasks/GC/DomainsFindOdd]: Нет доменов");
						case 'array':
							# строим массив доменов из биллинга
							$BillDomains = Array();
							foreach ($Domains as $Domain)
								$BillDomains[] = Mb_StrToLower($Domain['Domain'],'UTF-8');
							#-----------------------------------------------------------
							Debug("[comp/Tasks/GC/DomainsFindOdd]: доменов у регистратора " . SizeOf($RegDomains['Domains']) . "; в биллинге " . SizeOf($BillDomains));
							# сортируем и сравниваем массивы
							#Debug("[comp/Tasks/GC/DomainsFindOdd]: " . print_r($RegDomains['Domains'],true));
							#Debug("[comp/Tasks/GC/DomainsFindOdd]: " . print_r($BillDomains,true));
							ASort($RegDomains['Domains']);
							ASort($BillDomains);
							$DomainsOdd = Array_Diff($RegDomains['Domains'],$BillDomains);
							if(SizeOf($DomainsOdd) > 0){
								foreach($DomainsOdd as $DomainOdd){
									$Message = SPrintF('У регистратора %s найден лишний домен %s',$NowReg['Name'],$DomainOdd);
									Debug('[comp/Tasks/GC/DomainsFindOdd]: ' . $Message);
									$Event = Array('Text' => $Message,'PriorityID' => 'Error','IsReaded' => FALSE);
									$Event = Comp_Load('Events/EventInsert', $Event);
									if (!$Event)
										return ERROR | @Trigger_Error(500);
								}
							}
							break;
						default:
							return ERROR | @Trigger_Error(101);
						}
					}else{
						Debug(SPrintF('[comp/Tasks/GC/DomainsFindOdd]: У регистратора %s не найдено доменов',$NowReg['Name']));
						break;
					}
					#-----------------------------------------------------------
					break;
				default:
					return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
			}
			#-------------------------------------------------------------------
		}
		#-----------------------------------------------------------------------
		break;
	default:
	        return ERROR | @Trigger_Error(101);
}
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
return TRUE;

?>
