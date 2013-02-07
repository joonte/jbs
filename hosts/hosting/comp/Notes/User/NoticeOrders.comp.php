<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'`ID`','`DaysRemainded`','`ExpirationDate`','StatusID',
			'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Code`',
			'(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Name`',
			'(SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `ConsiderType`'
		);
$Where = Array(
			'`UserID` = @local.__USER_ID',
		);
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	foreach($Orders as $Order){
		#---------------------------------------------------------------------------
		$Number = Comp_Load('Formats/Order/Number',$Order['ID']);
		if(Is_Error($Number))
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------------------
		Debug(SPrintF('[comp/Notes/User/NoticeOrders]: processing service "%s", status "%s", days %s, order #%s',$Order['Code'],$Order['StatusID'],$Order['DaysRemainded'],$Number));
		#-------------------------------------------------------------------------
		if($Order['StatusID'] == 'Active' && ($Order['DaysRemainded'] < 15 || Is_Null($Order['DaysRemainded']))){
			# проверяем как скоро заканчивается, и, не надо ли уведомлять о окончании
		
			# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
			if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
				if($Order['ExpirationDate'] < Time() + 15 * 24 * 3600){
					#-------------------------------------------------------------------------
					$Order['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
					#-------------------------------------------------------------------------
					if($Order['Code'] == 'Default' && $Order['ConsiderType'] != 'Upon'){
						#-------------------------------------------------------------------------
						$Path = System_Element('templates/modules/NoticeOrders.Active.Default.html');
						if(Is_Error($Path))
					        	return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$Number,$Order['DaysRemainded']));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ServiceOrderPay',{OrderID:%u});",$Order['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------
						$Result[] = $NoBody;
					}
					#-------------------------------------------------------------------------
					#-------------------------------------------------------------------------
					if($Order['Code'] == 'Domains'){
						# выбираем данные по этому домену
						$Columns = Array('ID','CONCAT(`DomainName`,".",(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`)) AS `DomainNameFull`');
						$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
						switch(ValueOf($DomainOrder)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------
							$Path = System_Element('templates/modules/NoticeOrders.Active.Domains.html');
							if(Is_Error($Path))
						        	return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------------
							$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(SPrintF($Parse,$DomainOrder['DomainNameFull'],$Order['DaysRemainded']));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainOrderPay',{DomainOrderID:%u});",$DomainOrder['ID'])),'[оплатить]')));
							#-------------------------------------------------------------------------
							$Result[] = $NoBody;
							break;
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------
					#-------------------------------------------------------------------------
					if($Order['Code'] == 'ISPsw'){
						#-------------------------------------------------------------------------
						$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('ID','DaysRemainded','IP','(SELECT `Name` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `SchemeName`','(SELECT `IsProlong` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `IsProlong`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
						#-------------------------------------------------------------------------
						switch(ValueOf($ISPswOrder)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------
							if($ISPswOrder['IsProlong'] == 'yes'){
								#-------------------------------------------------------------------------
								$Path = System_Element('templates/modules/NoticeOrders.Active.ISPsw.IsProlong.html');
								if(Is_Error($Path))
							        	return ERROR | @Trigger_Error(500);
								#-------------------------------------------------------------------------------
								$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
								$NoBody = new Tag('NOBODY');
								$NoBody->AddHTML(SPrintF($Parse,$ISPswOrder['SchemeName'],$ISPswOrder['DaysRemainded']));
								$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
							}else{
								#-------------------------------------------------------------------------
								$Path = System_Element('templates/modules/NoticeOrders.Active.ISPsw.IsNoProlong.html');
								if(Is_Error($Path))
							        	return ERROR | @Trigger_Error(500);
								#-------------------------------------------------------------------------------
								$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
								$NoBody = new Tag('NOBODY');
								$NoBody->AddHTML(SPrintF($Parse,$ISPswOrder['SchemeName']));
								$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderSchemeChange',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[сменить тариф]')));
							}
							#-------------------------------------------------------------------------
							$Result[] = $NoBody;
							break;
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------
					#-------------------------------------------------------------------------
					if($Order['Code'] == 'DS'){
						#-------------------------------------------------------------------------
						$DSOrder = DB_Select('DSOrdersOwners',Array('ID','IP','DaysRemainded','(SELECT `Name` FROM `DSSchemes` WHERE `DSOrdersOwners`.`SchemeID` = `DSSchemes`.`ID`) as `SchemeName`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
						switch(ValueOf($DSOrder)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------------
							$Path = System_Element('templates/modules/NoticeOrders.Active.DS.html');
							if(Is_Error($Path))
						        	return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------------
							$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(SPrintF($Parse,$DSOrder['IP'],$DSOrder['SchemeName'],$DSOrder['DaysRemainded']));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DSOrderPay',{OrderID:%u});",$Order['ID'])),'[оплатить]')));
							#-------------------------------------------------------------------------
							$Result[] = $NoBody;
							break;
						default:
							return ERROR | @Trigger_Error(101);
						}
						#-------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------
				# данные услуги - имя юзера, домен, тариф  ...
				$Columns = Array(
						'ID','Login',
						SPrintF('(SELECT `Name` FROM `%1$sSchemes` WHERE `%1$sOrdersOwners`.`SchemeID` = `%1$sSchemes`.`ID`) as `SchemeName`',$Order['Code']),
						SPrintF('(SELECT `IsProlong` FROM `%1$sSchemes` WHERE `%1$sOrdersOwners`.`SchemeID` = `%1$sSchemes`.`ID`) as `IsProlong`',$Order['Code']),
						);
				$ServiceOrder = DB_Select(SPrintF('%sOrdersOwners',$Order['Code']),$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
				#-------------------------------------------------------------------------
				switch(ValueOf($Orders)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
        	                #-------------------------------------------------------------------------
				#-------------------------------------------------------------------------
				# В зависимости от того разрешено продление, или нет - выводим разный текст.
				if($ServiceOrder['IsProlong'] == 'yes'){
					#-------------------------------------------------------------------------------
					$Path = System_Element('templates/modules/NoticeOrders.Active.Hosting.IsProlong.html');
					if(Is_Error($Path))
				        	return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$ServiceOrder['Login'],$ServiceOrder['SchemeName'],$Order['DaysRemainded']));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------
				}else{
	        	                #-------------------------------------------------------------------------
					$Path = System_Element('templates/modules/NoticeOrders.Active.Hosting.IsNoProlong.html');
					if(Is_Error($Path))
				        	return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$ServiceOrder['Login'],$ServiceOrder['SchemeName']));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderSchemeChange',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[сменить тариф]')));
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
				$Result[] = $NoBody;
			}
			#-------------------------------------------------------------------------
			$DaysRemainded = $Order['DaysRemainded'];
			#-------------------------------------------------------------------------
			#-------------------------------------------------------------------------
		}elseif($Order['StatusID'] == 'Suspended'){
			# уведомляем что залочен, скоро будет удалён
			#---------------------------------------------------------------------------
			$Number = Comp_Load('Formats/Order/Number',$Order['ID']);
			if(Is_Error($Number))
				return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------------------------
			Debug(SPrintF('[comp/Notes/User/NoticeDeleted]: processing service %s, order %s',$Order['Code'],$Number));
			#-------------------------------------------------------------------------
			# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
			if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
				#-------------------------------------------------------------------------
				$Order['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'Default'){
					#-------------------------------------------------------------------------------
					$Path = System_Element('templates/modules/NoticeOrders.Suspended.Default.html');
					if(Is_Error($Path))
				        	return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$Number));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ServiceOrderPay',{OrderID:%u});",$Order['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------
					$Result[] = $NoBody;
				}
				#-------------------------------------------------------------------------
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'Domains'){
					# выбираем данные по этому домену
					$Columns = Array('ID','CONCAT(`DomainName`,".",(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`)) AS `DomainNameFull`');
					$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
					switch(ValueOf($DomainOrder)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------
						$Path = System_Element('templates/modules/NoticeOrders.Suspended.Domains.html');
						if(Is_Error($Path))
					        	return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(SPrintF($Parse,$DomainOrder['DomainNameFull']));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainOrderPay',{DomainOrderID:%u});",$DomainOrder['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------
						$Result[] = $NoBody;
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'ISPsw'){
					#-------------------------------------------------------------------------
					$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('ID','DaysRemainded','IP','(SELECT `Name` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `SchemeName`','(SELECT `IsProlong` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `IsProlong`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
					#-------------------------------------------------------------------------
					switch(ValueOf($ISPswOrder)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						$NoBody = new Tag('NOBODY');
						#-------------------------------------------------------------------------
						$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что истёк срок действия заказа на программное обеспечение ISPsystem, тариф '));
						$NoBody->AddChild(new Tag('STRONG',SPrintF('"%s".',$ISPswOrder['SchemeName'])));
						if($ISPswOrder['IsProlong'] == 'yes'){
							#-------------------------------------------------------------------------
							$Path = System_Element('templates/modules/NoticeOrders.Suspended.ISPsw.IsProlong.html');
							if(Is_Error($Path))
						        	return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------------
							$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(SPrintF($Parse,$ISPswOrder['SchemeName']));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
						}else{
							#-------------------------------------------------------------------------
							$Path = System_Element('templates/modules/NoticeOrders.Suspended.ISPsw.IsNoProlong.html');
							if(Is_Error($Path))
						        	return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------------
							$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(SPrintF($Parse,$ISPswOrder['SchemeName']));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]')));
						}
						#-------------------------------------------------------------------------
						$Result[] = $NoBody;
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'DS'){
					#-------------------------------------------------------------------------
					$DSOrder = DB_Select('DSOrdersOwners',Array('ID','IP','DaysRemainded','(SELECT `Name` FROM `DSSchemes` WHERE `DSOrdersOwners`.`SchemeID` = `DSSchemes`.`ID`) as `SchemeName`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
					switch(ValueOf($DSOrder)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------
						$Path = System_Element('templates/modules/NoticeOrders.Suspended.DS.html');
						if(Is_Error($Path))
					        	return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(SPrintF($Parse,$DSOrder['IP'],$DSOrder['SchemeName']));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DSOrderPay',{OrderID:%u});",$Order['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------
						$Result[] = $NoBody;
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------
				# данные услуги - имя юзера, домен, тариф  ...
				$Columns = Array(
						'ID','Login',
						SPrintF('(SELECT `Name` FROM `%1$sSchemes` WHERE `%1$sOrdersOwners`.`SchemeID` = `%1$sSchemes`.`ID`) as `SchemeName`',$Order['Code']),
						SPrintF('(SELECT `IsProlong` FROM `%1$sSchemes` WHERE `%1$sOrdersOwners`.`SchemeID` = `%1$sSchemes`.`ID`) as `IsProlong`',$Order['Code']),
						);
	
				$ServiceOrder = DB_Select(SPrintF('%sOrdersOwners',$Order['Code']),$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
				#-------------------------------------------------------------------------
				switch(ValueOf($Orders)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
        	                #-------------------------------------------------------------------------
				# В зависимости от того разрешено продление, или нет - выводим разный текст.
				if($ServiceOrder['IsProlong'] == 'yes'){
					#-------------------------------------------------------------------------
					$Path = System_Element('templates/modules/NoticeOrders.Suspended.Hosting.IsProlong.html');
					if(Is_Error($Path))
					       	return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$ServiceOrder['Login'],$ServiceOrder['SchemeName']));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------
					$Path = System_Element('templates/modules/NoticeOrders.Suspended.Hosting.IsNoProlong.html');
					if(Is_Error($Path))
					       	return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Parse = SPrintF('<NOBODY>%s</NOBODY>',Trim(IO_Read($Path)));
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(SPrintF($Parse,$Order['Name'],$ServiceOrder['Login'],$ServiceOrder['SchemeName']));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]')));
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
				$Result[] = $NoBody;
			}
			#-------------------------------------------------------------------------
			$DaysRemainded = $Order['DaysRemainded'];
			#-------------------------------------------------------------------------




                }elseif($Order['StatusID'] == 'Waiting'){
			# уведомление о неоплаченном заказе
		}else{
			# ничё не делаем?
		}
		#---------------------------------------------------------------------------
	}
	#---------------------------------------------------------------------------
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------

?>

