<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = Array(
		"`StatusID` = 'Waiting'",
		SPrintF('`StatusDate` BETWEEN (UNIX_TIMESTAMP() - (%d+1)*86400) AND (UNIX_TIMESTAMP() - %d*86400)',$Params['Invoices']['DaysBeforeNotice'],$Params['Invoices']['DaysBeforeNotice'])
		);
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('*'),Array('SortOn'=>'CreateDate','IsDesc'=>TRUE,'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	#---------------------------------------------------------------------------
	foreach($Invoices as $Invoice){
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[Tasks/GC/NotifyWaitingInvoice]: Уведомление о неоплаченном счёте #%d",$Invoice['ID']));
		#-------------------------------------------------------------------------------
		# достаём, что именно оплачивается счётом
		$Columns = Array(
				'*',
				'(SELECT `NameShort` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID` = `InvoicesItems`.`ServiceID`) AS `ServiceName`',
				'(SELECT `Code` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID` = `InvoicesItems`.`ServiceID`) AS `ServiceCode`',
				);
		$InvoicesItems = DB_Select('InvoicesItems',$Columns,Array('Where'=>SPrintF('`InvoiceID` = %u',$Invoice['ID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($InvoicesItems)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# счёт на поплнение? нету таких в соверменности, и быть не должно.
			continue 2;
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
                #-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Currency',$Invoice['Summ']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Items = Array();
		#-------------------------------------------------------------------------------
		foreach($InvoicesItems as $InvoicesItem){
			#-------------------------------------------------------------------------------
			$Comment = ($InvoicesItem['Comment'])?SPrintF('%s / ',$InvoicesItem['Comment']):'';
			#-------------------------------------------------------------------------------
			$Summ = Comp_Load('Formats/Currency',$InvoicesItem['Summ']);
			if(Is_Error($Summ))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Items[] = SPrintF("\t* %s / %s%s", $InvoicesItem['ServiceName'],$Comment,$Summ);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// генерируем ссылку на оплату
		$PaymentLink = SPrintF('%s://%s/Invoices/%u/',Url_Scheme(),HOST_ID,$Invoice['ID']);
		#-------------------------------------------------------------------------------
		#----------------------------------TRANSACTION----------------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/NotifyWaitingInvoice'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$IsSend = NotificationManager::sendMsg(new Message('NotPayedInvoice',(integer)$Invoice['UserID'],Array('Theme'=>SPrintF('Неоплаченный счёт #%d',$Invoice['ID']),'InvoiceID'=>$Invoice['ID'],'Items'=>Implode("\n",$Items),'PaymentLink'=>$PaymentLink)));
		#-------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'true':
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $Invoice['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Уведомление о неоплаченном счёте #%d, неоплачен более %d дней',$Invoice['ID'],$Params['Invoices']['DaysBeforeNotice'])
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			#-------------------------------------------------------------------------------
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'exception':
			#-------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $Invoice['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Уведомление о неоплаченном счёте #%d не доставлено. Не удалось оповестить пользователя ни одним из методов.',$Invoice['ID'])
					);
			#-------------------------------------------------------------------------------
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(500);
		}
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
