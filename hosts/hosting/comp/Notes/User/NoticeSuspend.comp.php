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
			'`ID`','`DaysRemainded`',
			'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Code`',
			'(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Name`',
		);
$Where = Array(
			'`UserID` = @local.__USER_ID',
			"`StatusID` = 'Active'",
			'`DaysRemainded` < 15 OR `DaysRemainded` IS NULL'
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
		Debug(SPrintF('[comp/Notes/User/NoticeSuspend]: processing service %s, days %s, order %s',$Order['Code'],$Order['DaysRemainded'],$Order['ID']));
		#-------------------------------------------------------------------------
		# заказы настриваемых услуг и сильно отличающихся от хостинга - обрабатываем отдельно
		if(In_Array($Order['Code'],Array('Default','Domains','ISPsw','DS'))){


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
			$NoBody->AddChild(new Tag('SPAN','Обращаем Ваше внимание, что истекает срок действия заказа на услугу,'));
			#-------------------------------------------------------------------------
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s"',$Order['Name'])));
			#-------------------------------------------------------------------------
			$NoBody->AddChild(new Tag('SPAN',', заказ '));
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s"',$ServiceOrder['Login'])));
			$NoBody->AddChild(new Tag('SPAN',', тарифный план '));
			$NoBody->AddChild(new Tag('STRONG',SPrintF(' "%s".',$ServiceOrder['SchemeName'])));
			#$NoBody->AddChild(new Tag('SPAN',SPrintF(', заказ "%s", тарифный план "%s".',$ServiceOrder['Login'],$ServiceOrder['SchemeName'])));
			#-------------------------------------------------------------------------
			# В зависимости от того разрешено продление, или нет - выводим разный текст.
			if($ServiceOrder['IsProlong'] == 'yes'){
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN',SPrintF('В случае не поступления оплаты в течение %s дня(ей) он будет заблокирован.',$Order['DaysRemainded'])));
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','Для того, чтобы осуществить оплату сейчас, нажмите на кнопку '));
				#-------------------------------------------------------------------------
				$A = new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderPay',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[оплатить]'));
				#-------------------------------------------------------------------------
				$NoBody->AddChild($A);
				#-------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','Используемый тарифный план не позволяет продление, но, вы можете сменить его на другой.'));
				#-------------------------------------------------------------------------
				$NoBody->AddChild(new Tag('SPAN','Для смены тарифного плана, нажмите на кнопку '));
				#-------------------------------------------------------------------------
				$A = new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/%sOrderSchemeChange',{%sOrderID:%u});",$Order['Code'],$Order['Code'],$ServiceOrder['ID'])),'[сменить тариф]'));
				#-------------------------------------------------------------------------
				$NoBody->AddChild($A);
			}
			#-------------------------------------------------------------------------
			$Result[] = $NoBody;
		}

		#-------------------------------------------------------------------------
		$DaysRemainded = $Order['DaysRemainded'];
		#-------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#$Parse = <<<EOD
#<NOBODY>
#<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на услугу '%s'</SPAN>
#<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
#<SPAN> с тарифным планом </SPAN>
#<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
#<SPAN> и в случае не поступления оплаты в течение </SPAN>
#<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
#<SPAN> дня(ей) он будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
#<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/HostingOrderPay',{HostingOrderID:%u});">[оплатить]</A>
#</NOBODY>
#EOD;
#}
#-------------------------------------------------------------------------------
#$NoBody->AddHTML(SPrintF($Parse,$Order['Code'],'Login','SchemeName',$DaysRemainded?$DaysRemainded:'сегодняшнего',$Order['ID']));
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
