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
$Columns = Array(
			'`ID`','`DaysRemainded`','`ExpirationDate`',
			'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Code`',
			'(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Name`',
		);
$Where = Array(
			'`UserID` = @local.__USER_ID',
			"`StatusID` = 'Suspended'",
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
		Debug(SPrintF('[comp/Notes/User/NoticeDeleted]: processing service %s, order %s',$Order['Code'],$Number));
		#-------------------------------------------------------------------------
		# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
		if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){
			#-------------------------------------------------------------------------
			$Order['DaysRemainded'] = Ceil(($Order['ExpirationDate'] - Time())/(24*3600));
			#-------------------------------------------------------------------------
			if($Order['Code'] == 'Default'){
				$NoBody = new Tag('NOBODY');
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что истёк срок действия заказа на услугу '));
				$NoBody->AddChild(new Tag('STRONG',SPrintF('"%s"',$Order['Name'])));
				$NoBody->AddChild(new Tag('SPAN',', заказ '));
				$NoBody->AddChild(new Tag('STRONG',SPrintF('#%s.',$Number)));
				$NoBody->AddChild(new Tag('SPAN','В случае не поступления оплаты он будет удалён.'));
				$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
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
					$NoBody = new Tag('NOBODY');
					#-------------------------------------------------------------------------
					$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что истёк срок действия заказа на домен '));
					$NoBody->AddChild(new Tag('STRONG',SPrintF('"%s".',$DomainOrder['DomainNameFull'])));
					$NoBody->AddChild(new Tag('SPAN','В случае не поступления оплаты он будет удалён, и его смогут зарегистрировать другие люди.'));
					$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
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
						$NoBody->AddChild(new Tag('SPAN',SPrintF('В случае не поступления оплаты он будет удалён.',$ISPswOrder['DaysRemainded'])));
						$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
						$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});",$ISPswOrder['ID'])),'[оплатить]')));
					}else{
						$NoBody->AddChild(new Tag('SPAN','Используемый тарифный план не позволяет продление, но, вы можете сменить его на другой.'));
						$NoBody->AddChild(new Tag('SPAN','Для смены тарифного плана, обратитесь '));
						$NoBody->AddChild(new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]'));
						$NoBody->AddChild(new Tag('SPAN','с просьбой разблокировать аккаунт.'));
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
					$NoBody = new Tag('NOBODY');
					#-------------------------------------------------------------------------
					$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что истёк срок аренды заказанного Вами сервера, IP адрес '));
					$NoBody->AddChild(new Tag('STRONG',SPrintF('"%s"',$DSOrder['IP'])));
					$NoBody->AddChild(new Tag('SPAN',' с тарифным планом '));
					$NoBody->AddChild(new Tag('STRONG',SPrintF('%s.',$DSOrder['SchemeName'])));
					$NoBody->AddChild(new Tag('SPAN','В случае не поступления оплаты, все ваши данные будут с него удалены.'));
					$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
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
			$NoBody = new Tag('NOBODY');
			#-------------------------------------------------------------------------
			$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что окончился срок заказа на услугу,'));
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s"',$Order['Name'])));
			$NoBody->AddChild(new Tag('SPAN',', заказ '));
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s"',$ServiceOrder['Login'])));
			$NoBody->AddChild(new Tag('SPAN',', тарифный план '));
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s".',$ServiceOrder['SchemeName'])));
			#-------------------------------------------------------------------------
			# В зависимости от того разрешено продление, или нет - выводим разный текст.
			if($ServiceOrder['IsProlong'] == 'yes'){
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','В случае не поступления оплаты он будет удалён.'));
				$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
				$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]')));
				#-------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','Используемый тарифный план не позволяет продление, но, вы можете сменить его на другой.'));
				$NoBody->AddChild(new Tag('SPAN','Для смены тарифного плана, обратитесь '));
				$NoBody->AddChild(new Tag('A',Array('href'=>SPrintF("/Tickets")),'[систему тикетов]'));
				$NoBody->AddChild(new Tag('SPAN','с просьбой разблокировать аккаунт.'));
				#-------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------
			$Result[] = $NoBody;
		}
		#-------------------------------------------------------------------------
		$DaysRemainded = $Order['DaysRemainded'];
		#-------------------------------------------------------------------------
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
