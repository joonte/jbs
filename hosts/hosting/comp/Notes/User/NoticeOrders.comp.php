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
			'(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `NameShort`',
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
		#-------------------------------------------------------------------------------
		$Number = Comp_Load('Formats/Order/Number',$Order['ID']);
		if(Is_Error($Number))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# параметры для замены в шаблонах
		$Params = Array('Order'=>$Order,'Number'=>$Number);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/Notes/User/NoticeOrders]: service "%s", status "%s", days %s, order #%s',$Order['Code'],$Order['StatusID'],$Order['DaysRemainded'],$Number));
		#-------------------------------------------------------------------------------
		if($Order['StatusID'] == 'Active' && ($Order['DaysRemainded'] < 15 || Is_Null($Order['DaysRemainded']))){
			# проверяем как скоро заканчивается, и, не надо ли уведомлять о окончании
		
			# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
			if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
				if($Order['ExpirationDate'] < Time() + 15 * 24 * 3600){
					#-------------------------------------------------------------------------
					$Order['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
					$Params['Order']['DaysRemainded'] = $Order['DaysRemainded'];
					#-------------------------------------------------------------------------
					if($Order['Code'] == 'Default' && $Order['ConsiderType'] != 'Upon'){
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.Default',$Params));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ServiceOrderPay',{ServiceOrderID:%u});",$Order['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------------
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
							#-------------------------------------------------------------------------------
							$Params['DomainOrder'] = $DomainOrder;
							#-------------------------------------------------------------------------------
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.Domains',$Params));
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
						$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('ID','DaysRemainded','IP','(SELECT `Name` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `SchemeName`','(SELECT `IsProlong` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `IsProlong`','(SELECT `ConsiderTypeID` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `ConsiderTypeID`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
						#-------------------------------------------------------------------------
						switch(ValueOf($ISPswOrder)){
						case 'error':
							return ERROR | @Trigger_Error(500);
						case 'exception':
							return ERROR | @Trigger_Error(400);
						case 'array':
							#-------------------------------------------------------------------------
							# нечего напоминать о вечном =))
							if($ISPswOrder['ConsiderTypeID'] == 'Upon')
								break;
							#-------------------------------------------------------------------------
							$Params['ISPswOrder'] = $ISPswOrder;
							#-------------------------------------------------------------------------
							$NoBody = new Tag('NOBODY');
							#-------------------------------------------------------------------------
							if($ISPswOrder['IsProlong'] == 'yes'){
								#-------------------------------------------------------------------------------
								$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.ISPsw.IsProlong',$Params));
								$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
							}else{
								#-------------------------------------------------------------------------------
								$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.ISPsw.IsNoProlong',$Params));
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
							$Params['DSOrder'] = $DSOrder;
							#-------------------------------------------------------------------------------
							$NoBody = new Tag('NOBODY');
							$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.DS',$Params));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DSOrderPay',{OrderID:%u});",$Order['ID'])),'[оплатить]')));
							#-------------------------------------------------------------------------
							$Result[] = $NoBody;
							#-------------------------------------------------------------------------------
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
					#-------------------------------------------------------------------------------
					$Params['ServiceOrder'] = $ServiceOrder;
					#-------------------------------------------------------------------------------
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
        	                #-------------------------------------------------------------------------
				#-------------------------------------------------------------------------
				# В зависимости от того разрешено продление, или нет - выводим разный текст.
				$NoBody = new Tag('NOBODY');
				#-------------------------------------------------------------------------
				if($ServiceOrder['IsProlong'] == 'yes'){
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.Hosting.IsProlong',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Active.Hosting.IsNoProlong',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderSchemeChange',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[сменить тариф]')));
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
				$Result[] = $NoBody;
			}
			#-------------------------------------------------------------------------
		}elseif($Order['StatusID'] == 'Suspended'){
			# уведомляем что залочен, скоро будет удалён
			#---------------------------------------------------------------------------
			# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
			if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
				#-------------------------------------------------------------------------
				$Order['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
				$Params['Order']['DaysRemainded'] = $Order['DaysRemainded'];
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'Default'){
					#-------------------------------------------------------------------------------
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.Default',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ServiceOrderPay',{ServiceOrderID:%u});",$Order['ID'])),'[оплатить]')));
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
						#------------------------------------------------------------------------------
						$Params['DomainOrder'] = $DomainOrder;
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.Domains',$Params));
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
						$NoBody = new Tag('NOBODY');
						#-------------------------------------------------------------------------
						$Params['ISPswOrder'] = $ISPswOrder;
						#-------------------------------------------------------------------------
						if($ISPswOrder['IsProlong'] == 'yes'){
							#-------------------------------------------------------------------------------
							$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.ISPsw.IsProlong',$Params));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
							#-------------------------------------------------------------------------------
						}else{
							#-------------------------------------------------------------------------------
							$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.ISPsw.IsNoProlong',$Params));
							$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]')));
							#-------------------------------------------------------------------------------
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
						$Params['DSOrder'] = $DSOrder;
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.DS',$Params));
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
					#-------------------------------------------------------------------------------
					$Params['ServiceOrder'] = $ServiceOrder;
					#-------------------------------------------------------------------------------
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
        	                #-------------------------------------------------------------------------
				# В зависимости от того разрешено продление, или нет - выводим разный текст.
				$NoBody = new Tag('NOBODY');
				#-------------------------------------------------------------------------
				if($ServiceOrder['IsProlong'] == 'yes'){
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.Hosting.IsProlong',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Suspended.Hosting.IsNoProlong',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]')));
					#-------------------------------------------------------------------------------
				}
				#------------------------------------------------------------------------------
				$Result[] = $NoBody;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------
                }elseif($Order['StatusID'] == 'Waiting'){
			# уведомление о неоплаченном заказе
			#---------------------------------------------------------------------------
			# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
			if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
				#-------------------------------------------------------------------------
				$Order['DaysRemainded'] = $Params['Order']['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
				#-------------------------------------------------------------------------
				if($Order['Code'] == 'Default'){
					#-------------------------------------------------------------------------------
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Waiting.Default',$Params));
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
						$Params['DomainOrder'] = $DomainOrder;
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Waiting.Domains',$Params));
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
						#-------------------------------------------------------------------------------
						$Params['ISPswOrder'] = $ISPswOrder;
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Waiting.ISPsw',$Params));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------------
						$Result[] = $NoBody;
						#-------------------------------------------------------------------------------
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
						$Params['DSOrder'] = $DSOrder;
						#-------------------------------------------------------------------------------
						$NoBody = new Tag('NOBODY');
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Waiting.DS',$Params));
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
					#-------------------------------------------------------------------------------
					$Params['ServiceOrder'] = $ServiceOrder;
					#-------------------------------------------------------------------------------
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------
				$NoBody = new Tag('NOBODY');
				$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.Waiting.Hosting',$Params));
				$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
				#-------------------------------------------------------------------------
				$Result[] = $NoBody;
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}elseif($Order['StatusID'] == 'ClaimForRegister'){
			#-------------------------------------------------------------------------------
			$Columns = Array('ID','PersonID','ProfileID','CONCAT(`DomainName`,".",(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`)) AS `DomainNameFull`');
			$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
			switch(ValueOf($DomainOrder)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				if(Is_Null($DomainOrder['ProfileID']) && !$DomainOrder['PersonID']){
					#-------------------------------------------------------------------------------
					$Params['DomainOrder'] = $DomainOrder;
					#-------------------------------------------------------------------------------
					$NoBody = new Tag('NOBODY');
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.ClaimForRegister',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainSelectOwner',{DomainOrderID:%u});",$DomainOrder['ID'])),'[определить]')));
					#-------------------------------------------------------------------------
					$Result[] = $NoBody;
				}
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}elseif($Order['StatusID'] == 'ForTransfer'){
			#-------------------------------------------------------------------------------
			$Columns = Array('ID','AuthInfo','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) AS `Name`','(SELECT `CostTransfer` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) AS `CostTransfer`');
			#-------------------------------------------------------------------------------
			$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
			switch(ValueOf($DomainOrder)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------
				$NoBody = new Tag('NOBODY');
				#-------------------------------------------------------------------------------
				$Params['DomainOrder'] = $DomainOrder;
				#-------------------------------------------------------------------------
				if(In_Array($DomainOrder['Name'],Array('ru','su','рф'))){
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.ForTransfer.USSR',$Params));
					$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainOrderPay',{DomainOrderID:%u});",$DomainOrder['ID'])),'[оплатить]')));
					#-------------------------------------------------------------------------
				}else{
					# два варианта - зависит от наличия AuthInfo
					if($DomainOrder['AuthInfo']){
						#-------------------------------------------------------------------------
						$Summ = Comp_Load('Formats/Currency',$DomainOrder['CostTransfer']);
						if(Is_Error($Summ))
							return ERROR | @Trigger_Error(500);
						#------------------------------------------------------------------------------
						$Params['Summ'] = $Summ;
						#-------------------------------------------------------------------------------
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.ForTransfer.bourgeois.AuthInfo',$Params));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainOrderPay',{DomainOrderID:%u});",$DomainOrder['ID'])),'[оплатить]')));
						#-------------------------------------------------------------------------
					}else{
						#-------------------------------------------------------------------------------
						$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.ForTransfer.bourgeois.NoAuthInfo',$Params));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/DomainAuthInfoInput',{DomainOrderID:%u});",$DomainOrder['ID'])),'[ввести пароль домена]')));
					#-------------------------------------------------------------------------
					}
				}
				#-------------------------------------------------------------------------
				$Result[] = $NoBody;
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------

		}elseif($Order['StatusID'] == 'OnTransfer'){
			#-------------------------------------------------------------------------------
			$Columns = Array('ID','AuthInfo','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) AS `Name`','StatusDate');
			$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
			switch(ValueOf($DomainOrder)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				$NoBody = new Tag('NOBODY');
				#-------------------------------------------------------------------------------
				$Params['TransferDaysRemainded'] = Ceil(($DomainOrder['StatusDate'] + 180*24*3600 - Time())/(24*3600));
				$Params['DomainOrder'] = $DomainOrder;
				#-------------------------------------------------------------------------------
				if(In_Array($DomainOrder['Name'],Array('ru','su','рф'))){
					#-------------------------------------------------------------------------------
					$NoBody->AddHTML(TemplateReplace('Notes.User.NoticeOrders.OnTransfer.USSR',$Params));
					#-------------------------------------------------------------------------------
					$Result[] = $NoBody;
					#-------------------------------------------------------------------------------
				}else{
					# ничё?
				}
				#-------------------------------------------------------------------------
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------

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

