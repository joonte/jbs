<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Registrator.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['DomainsOrdersRegStatusUpdate'];
#-------------------------------------------------------------------------------
# Выкручиваемся чтобы не пометить удаленными домены предназначенные для блокировки
$Where = "`StatusID` = 'Active' AND UNIX_TIMESTAMP() - `RegUpdateDate` > " . $Settings['SleepTime'] . " AND UNIX_TIMESTAMP() - `StatusDate` > 86400*2";
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainsOrders','ID',Array('Where'=>$Where,'Limits'=>Array(0,5)));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = Array();
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
	  #-------------------------------------------------------------------------
	  $DomainOrder = DB_Select('DomainsOrdersOwners',Array('ID','UserID','DomainName','SchemeID','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$DomainOrder['ID']));
	  #-------------------------------------------------------------------------
	  switch(ValueOf($DomainOrder)){
		  case 'error':
			return ERROR | @Trigger_Error(500);
		  case 'exception':
			return new gException('DOMAIN_ORDER_NOT_FOUND','Заказ домена не найден');
		  case 'array':
		    #-------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'][] = SPrintF('%s.%s',Mb_StrToLower($DomainOrder['DomainName'],'UTF-8'),$DomainOrder['SchemeName']);
			#-------------------------------------------------------------------
			$RegistratorID = DB_Select('DomainsSchemes','RegistratorID as ID',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
			$RegistratorName = DB_Select('Registrators','Name',Array('UNIQ','ID'=>$RegistratorID['ID']));
			$Registrator = new Registrator();
			$IsSelected = $Registrator->Select((integer)$RegistratorID['ID']);
			#-------------------------------------------------------------------
			switch(ValueOf($IsSelected)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
				case 'true':
					Debug("[comp/Tasks/DomainsOrdersRegStatusUpdate]: DomainName is " . SPrintF('%s.%s',Mb_StrToLower($DomainOrder['DomainName'],'UTF-8'),$DomainOrder['SchemeName']));
					#-----------------------------------------------------------
					$Available = $Registrator->IsAvailableDomain(SPrintF('%s.%s',Mb_StrToLower($DomainOrder['DomainName'],'UTF-8'),$DomainOrder['SchemeName']));
					#-----------------------------------------------------------
					break;
				default:
					return new gException('WRONG_STATUS','Регистратор не определён');
			}
			#-------------------------------------------------------------------
			switch(ValueOf($Available)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					#-----------------------------------------------------------
					switch($Available->CodeID){
						case 'REGISTRATOR_ERROR':
							Debug(SPrintF('[comp/Tasks/DomainsOrdersRegStatusUpdate]: Регистратор ID %d: %s',$RegistratorID['ID'],$Available->String));
							break;
						default:
							Debug(SPrintF('[comp/Tasks/DomainsOrdersRegStatusUpdate]: Для регистратора ID %d проверка домена не реализована.',$RegistratorID['ID']));
							$IsUpdate = DB_Update('DomainsOrders',Array('RegUpdateDate'=>Time()),Array('ID'=>$DomainOrder['ID']));
							if(Is_Error($IsUpdate))
								return ERROR | @Trigger_Error(500);
					}
					#-----------------------------------------------------------
					break;
				case 'array':
					#-----------------------------------------------------------
					Debug("[comp/Tasks/DomainsOrdersRegStatusUpdate]: Available is " . $Available['Status'] );
					#-----------------------------------------------------------
					switch ($Available['Status']) {
					  case 'true':
						Debug("[comp/Tasks/DomainsOrdersRegStatusUpdate]: Service_ID is " . $Available['ServiceID'] );
					  break;
					  case 'false':
						Debug("[comp/Tasks/DomainsOrdersRegStatusUpdate]: ErrorText is " . $Available['ErrorText'] );
						#-------------------------------------------------------
						$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'Deleted','RowsIDs'=>$DomainOrder['ID'],'Comment'=>SPrintF('Заказ домена не найден у регистратора %s.',$RegistratorName['Name'])));
						#-------------------------------------------------------
						break;
						default:
							return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
					}
					break;
				default:
					return new gException('WRONG_STATUS','Регистратор не определён');
			}
			#-------------------------------------------------------------------
			if(IsSet($Comp)){
				switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-----------------------------------------------------------
						$Event = Array(
								'UserID'	=> $DomainOrder['UserID'],
								'PriorityID'	=> 'Error',
								'Text'		=> SPrintF('Заказ домена %s.%s не найден у регистратора %s. Статус заказа изменен на "Удален".',$DomainOrder['DomainName'],$DomainOrder['SchemeName'],$RegistratorName['Name']),
								'IsReaded'	=> FALSE
								);
						$Event = Comp_Load('Events/EventInsert',$Event);
						if(!$Event)
							return ERROR | @Trigger_Error(500);
						#-----------------------------------------------------------
						break;
					default:
						return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------
				UnSet($Comp);
			}
			$IsUpdate = DB_Update('DomainsOrders',Array('RegUpdateDate'=>Time()),Array('ID'=>$DomainOrder['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------
			break;
		  default:
			return ERROR | @Trigger_Error(101);
	  }
	#---------------------------------------------------------------------------
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainsOrders',Array('Where'=>$Where));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return ($Count?120:MkTime(5,0,0,Date('n'),Date('j')+1,Date('Y')));
#-------------------------------------------------------------------------------

?>
