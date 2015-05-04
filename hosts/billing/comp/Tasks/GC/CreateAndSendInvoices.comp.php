<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['CreateAndSendInvoicesSettings'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
if(Date('N') != $Settings['CreateAndSendInvoicesDay'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'ID', 'UserID','IsAutoProlong','DaysRemainded',
			'(SELECT `Params` FROM `Users` WHERE `Users`.`ID` = `OrdersOwners`.`UserID`) AS `Params`',
			'(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `OrdersOwners`.`UserID`) AS `Email`',
			'(SELECT `Item` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Item`',
			'(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Name`',
			'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Code`',
			'(SELECT `ID` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `ServiceID`',
			'(SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `ConsiderTypeID`',
			'ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) AS `Remainded`'
		);
$Where = SPrintF("(SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) != 'Upon' AND `StatusID` = 'Active' AND ((ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) <= %u AND ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) > 0) OR (`DaysRemainded` <= %u AND `DaysRemainded` != 0))",$Settings['CreateAndSendInvoicesPeriod'],$Settings['CreateAndSendInvoicesPeriod']);
#-------------------------------------------------------------------------------
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$Handled = Array();
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: обработка юзера (%s)',$Order['Email']));
		#-------------------------------------------------------------------------------
		if(In_Array($Order['UserID'],$Handled))
			continue;
		#-------------------------------------------------------------------------------
		if(!$Order['IsAutoProlong']){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/CreateAndSendInvoices]: для заказа %s (%s) отключено автопродление',$Order['ID'],$Order['Item']));
			#-------------------------------------------------------------------------------
			continue;
			#---------------------------------------------------------------------------
		}
		#---------------------------------------------------------------------------
		#---------------------------------------------------------------------------
		if(IsSet($Order['Params']['NotCreateInvoicesAutomatically']) && $Order['Params']['NotCreateInvoicesAutomatically']){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/CreateAndSendInvoices]: настройки пользователя (%s) запрещают автоматическую выписку счетов',$Order['Email']));
			#-------------------------------------------------------------------------------
			continue;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# Чистим юзеру корзину
		$iBasket = DB_Select('BasketOwners','ID',Array('Where'=>SPrintF('`UserID` = %u',$Order['UserID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($iBasket)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
			break;
		case 'array':
			#---------------------------------------------------------------------------
			$Array = Array();
			foreach($iBasket as $Basket)
				$Array[] = $Basket['ID'];
			#---------------------------------------------------------------------------
			$IsDelete = DB_Delete('Basket',Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Array))));
			if(Is_Error($IsDelete))
				return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------------------------
			break;
			#---------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# Выбираем заказы этого юзера
		$Where = SPrintF("(SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) != 'Upon' AND `StatusID` = 'Active' AND ((ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) <= %u AND ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) > 0) OR (`DaysRemainded` <= %u AND `DaysRemainded` != 0)) AND `UserID` = %u",$Settings['CreateAndSendInvoicesPeriod'],$Settings['CreateAndSendInvoicesPeriod'],$Order['UserID']);
		#-------------------------------------------------------------------------------
		$UOrders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($UOrders)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: юзер (%s), необходимо продлить (%u) заказов',$Order['Email'],SizeOf($UOrders)));
			#-------------------------------------------------------------------------------
			$Handled[] = $Order['UserID'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		foreach($UOrders as $UOrder){
			#-------------------------------------------------------------------------------
			if(!$UOrder['IsAutoProlong'])
				continue;
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# проверка не выписан ли уже неоплаченный счёт на эту услугу
			$Where = SPrintF("`InvoicesItems`.`InvoiceID` = `Invoices`.`ID` AND `Invoices`.`StatusID` = 'Waiting' AND `InvoicesItems`.`ServiceID` = %u AND `InvoicesItems`.`OrderID` = %u",$UOrder['ServiceID'],$UOrder['ID']);
			#-------------------------------------------------------------------------------
			$Count = DB_Count(Array('Invoices','InvoicesItems'),Array('Where'=>$Where));
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($Count){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/CreateAndSendInvoices]: для юзера (%s), уже есть счёт на %s/#%u',$Order['Email'],$UOrder['Code'],$UOrder['ID']));
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: юзер (%s), необходимо продлить (%s) заказ #%u',$Order['Email'],$UOrder['Code'],$UOrder['ID']));
			#-------------------------------------------------------------------------------
			if($UOrder['Code'] == 'Default'){
				#-------------------------------------------------------------------------------
				# если подневно - на месяц, иначе - на 1 единицу
				$AmountPay = (($UOrder['ConsiderTypeID'] == 'Daily')?31:1);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/ServiceOrderPay',Array('ServiceOrderID'=>$UOrder['ID'],'AmountPay'=>$AmountPay,'IsUseBasket'=>TRUE,'PayMessage'=>'Автоматическое выставление счёта на продление услуг'));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			if($UOrder['Code'] == 'Domain'){
				#-------------------------------------------------------------------------------
				$OrderInfo = DB_Select(SPrintF('%sOrdersOwners',$UOrder['Code']),Array('ID','OrderID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$UOrder['ID'])));
				switch(ValueOf($OrderInfo)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/DomainOrderPay',Array('DomainOrderID'=>$OrderInfo['ID'],'YearsPay'=>1,'IsUseBasket'=>TRUE,'PayMessage'=>'Автоматическое выставление счёта на продление услуг'));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$OrderInfo = DB_Select(SPrintF('%sOrdersOwners',$UOrder['Code']),Array('ID','SchemeID',SPrintF('(SELECT `MinDaysPay` FROM `%sSchemes` WHERE `%sSchemes`.`ID` = `%sOrdersOwners`.`SchemeID`) as `MinDaysPay`',$UOrder['Code'],$UOrder['Code'],$UOrder['Code']),SPrintF('(SELECT `CostDay` FROM `%sSchemes` WHERE `%sSchemes`.`ID` = `%sOrdersOwners`.`SchemeID`) as `CostDay`',$UOrder['Code'],$UOrder['Code'],$UOrder['Code'])),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$UOrder['ID'])));
				switch(ValueOf($OrderInfo)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				# реализация JBS-948
				if(Is_Error(DB_Transaction($TransactionID = UniqID('CostPay1'))))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Services/Bonuses',$OrderInfo['MinDaysPay'],$UOrder['ServiceID'],$OrderInfo['SchemeID'],$UOrder['UserID'],'0.00',$OrderInfo['CostDay'],FALSE);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				if(Is_Error(DB_Roll($TransactionID)))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				if(Round($Comp['CostPay'],2) == 0){
					#-------------------------------------------------------------------------------
					Debug(SPrintF('[comp/www/CreateAndSendInvoices]: нулевая цена продления на %s дней, счёт не выписан',$OrderInfo['MinDaysPay']));
					#-------------------------------------------------------------------------------
					continue;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load(SPrintF('www/API/%sOrderPay',$UOrder['Code']),Array(SPrintF('%sOrderID',$UOrder['Code'])=>$OrderInfo['ID'],'DaysPay'=>$OrderInfo['MinDaysPay'],'IsUseBasket'=>TRUE,'PayMessage'=>'Автоматическое выставление счёта на продление услуг'));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# выписываем счета для юзера
		$Baskets = DB_Select('BasketOwners',Array('ID','ContractID','UserID','(SELECT `TypeID` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `TypeID`'),Array('Where'=>SPrintF('`UserID` = %u',$Order['UserID'])));
		switch(ValueOf($Baskets)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# соскакиваем - по какой-то причине в корзину ничё не уложилось
			continue 2;
		case 'array':
			#---------------------------------------------------------------------------
			break;
			#---------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#---------------------------------------------------------------------------
		$Contracts = Array();
		#---------------------------------------------------------------------------
		$Attachments = Array();
		#---------------------------------------------------------------------------
		foreach($Baskets as $Basket){
			#-------------------------------------------------------------------------------
			if(In_Array($Basket['ContractID'],$Contracts))
				continue;
			#-------------------------------------------------------------------------------
			if($Basket['TypeID'] == 'NaturalPartner')
				continue;
			#-------------------------------------------------------------------------------
			$Contracts[] = $Basket['ContractID'];
			#-------------------------------------------------------------------------------
			#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: юзер (%s) обработка корзины по договору (%s)',$Order['Email'],$Basket['ContractID']));
			#-------------------------------------------------------------------------------
			$PaymentSystems = $Config['Invoices']['PaymentSystems'];
			#-------------------------------------------------------------------------------
			$Array = Array();
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
				#-------------------------------------------------------------------------------
				if(!$PaymentSystems[$PaymentSystemID]['IsActive'])
					continue;
				#-------------------------------------------------------------------------------
				$Array[] = $PaymentSystemID;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			if(IsSet($Order['Params']['CreateInvoicesAutomatically'][$Basket['TypeID']]) && In_Array($Order['Params']['CreateInvoicesAutomatically'][$Basket['TypeID']],$Array)){
				#-------------------------------------------------------------------------------
				$PaymentSystemID = $Order['Params']['CreateInvoicesAutomatically'][$Basket['TypeID']];
				#-------------------------------------------------------------------------------

			}else{
				#-------------------------------------------------------------------------------
				# выбираем тип патёжной системы, которой был оплачен последний счёт юзера, по этому договору
				$Invoice = DB_Select('InvoicesOwners',Array('PaymentSystemID'),Array('UNIQ','Where'=>SPrintF('`StatusID` = "Payed" AND `ContractID` = %s AND `UserID` = %s',$Basket['ContractID'],$Order['UserID']),'Limits'=>Array('Start'=>0,'Length'=>1),'SortOn'=>'StatusDate','IsDesc'=>TRUE));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Invoice)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
					break;
				case 'array':
					#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: Invoice = %s',print_r($Invoice,true)));
					#-------------------------------------------------------------------------------
					if(IsSet($PaymentSystems[$Invoice['PaymentSystemID']]))
						$PaymentSystemID = $Invoice['PaymentSystemID'];
					#-------------------------------------------------------------------------------
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				if(!IsSet($PaymentSystemID)){
					#-------------------------------------------------------------------------------
					if(IsSet($PaymentSystems[$Basket['TypeID']])){
						#-------------------------------------------------------------------------------
						$PaymentSystemID = $Basket['TypeID'];
						#-------------------------------------------------------------------------------
					}else{
						#-------------------------------------------------------------------------------
						$PaymentSystemID = $Array[0];
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/InvoiceMake',Array('ContractID'=>$Basket['ContractID'],'PaymentSystemID'=>$PaymentSystemID,'PayMessage'=>'Автоматическое выставление счёта на продление услуг'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				break 2;
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#if(Is_Error($Comp))
			#	return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/CreateAndSendInvoices]: юзеру (%s) выписан счёт (%s)',$Order['Email'],$Comp['InvoiceID']));
			#-------------------------------------------------------------------------------
			# надо исключить вложение в писем счетов на вебмани и прочее - их нельзя напярмую оплачивать
			if(!$Settings['CreateAndSendInvoicesSendOnlyNatural']){
				#-------------------------------------------------------------------------------
				$Attachments[] = $Comp['InvoiceID'];
				#-------------------------------------------------------------------------------
			}elseif($Settings['CreateAndSendInvoicesSendOnlyNatural'] && $PaymentSystemID == $Basket['TypeID']){
				#-------------------------------------------------------------------------------
				$Attachments[] = $Comp['InvoiceID'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		UnSet($PaymentSystemID);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/www/CreateAndSendInvoices]: Attachments = %s',print_r($Attachments,true)));
		#-------------------------------------------------------------------------------
		# а вложений, в принципе, может и не быть. тогда слать ничё не надо - задача как раз слать счета
		if(SizeOf($Attachments) > 0 || $Settings['CreateAndSendInvoicesSendEmptyMail']){
			#-------------------------------------------------------------------------------
			# перебираем файлы, генерим письмо
			$EmailAttachments = Array();
			#-------------------------------------------------------------------------------
			foreach($Attachments as $InvoiceID){
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/InvoiceDownload',Array('InvoiceID'=>$InvoiceID,'IsStamp'=>TRUE,'IsNoHeaders'=>TRUE));
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$EmailAttachments[] = Array(
								'Name'	=> SPrintF('Invoice_%s.pdf',$InvoiceID),
								'Size'	=> StrLen($Comp),
								'Mime'	=> 'application/pdf; charset=utf-8',
								'Data'	=> Chunk_Split(Base64_Encode($Comp))
								);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# а вложений, в принципе, может и не быть ... 
			$msgParams = Array('EmailAttachments'=>(SizeOf($EmailAttachments) > 0)?$EmailAttachments:'не определено');
			#-------------------------------------------------------------------------------
			$msg = new Message('CreateAndSendInvoices', $Order['UserID'], $msgParams);
			$IsSend = NotificationManager::sendMsg($msg);
			#-------------------------------------------------------------------------------
			switch(ValueOf($IsSend)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'true':
				break;
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
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
