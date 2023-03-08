<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Invoice');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$PaymentSystemName = $Config['Invoices']['PaymentSystems'][$Invoice['PaymentSystemID']]['Name'];
#-------------------------------------------------------------------------------
#----------------------------------TRANSACTION----------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Triggers/Statuses/Invoices/Payed'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# костыль для JBS-1251 - 54-ФЗ
# достаём параметры платёжной системы - надо ли отправлять данные в кассу
#-------------------------------------------------------------------------------
if($Config['Invoices']['PaymentSystems'][$Invoice['PaymentSystemID']]['Is54-FZ']){
	#-------------------------------------------------------------------------------
	# проставляем параметры - отправлен ли чек
	$IsUpdate = DB_Update('Invoices',Array('IsCheckSent'=>FALSE),Array('ID'=>$Invoice['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Tasks',Array('ExecuteDate'=>Time()),Array('Where'=>"`TypeID` = 'Taxation'"));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// данные в метрику
$Settings = $Config['Interface']['User']['YandexMetrika'];
#-------------------------------------------------------------------------------
// если метрика НЕ включена, то всё
if($Settings['IsActive'] && $Settings['YandexCounterId'] && $Settings['Token']){
	#-------------------------------------------------------------------------------
	$Query = Array(
			'id'			=> $Invoice['ID'],
			'client_uniq_id'	=> $Invoice['UserID'],
			'client_type'		=> 'CONTACT',
			'create_date_time'	=> SPrintF('%s %s',Date('Y-m-d',$Invoice['CreateDate']),Date('G:i:s',$Invoice['CreateDate'])),
			'order_status'		=> 'PAID',
			'revenue'		=> $Invoice['Summ'],
			'cost'			=> 0,
			'finish_date_time'	=> SPrintF('%s %s',Date('Y-m-d',Time()),Date('G:i:s',Time())),
			);
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('TmpData',Array('UserID'=>$Invoice['UserID'],'AppID'=>'YandexMetrika','Col1'=>'Orders','Params'=>$Query));
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Invoice['IsPosted']){
	#-------------------------------------------------------------------------------
	Debug(SprintF('[comp/Triggers/Statuses/Invoices/Payed]: IsPosted = TRUE'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
if(Is_Error($Number))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$Invoice['ContractID'],'Summ'=>$Invoice['Summ'],'ServiceID'=>1000,'Comment'=>SPrintF('по счёту №%s',$Number)));
#-------------------------------------------------------------------------------
switch(ValueOf($IsUpdate)){
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
$Event = Array(
		'UserID'	=> $Invoice['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF('Оплачен счёт №%s, на сумму %s, платежная система (%s)',$Number,$Invoice['Summ'],$PaymentSystemName)
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// TODO разобраться, нахрена тут выбирается ServiceID из сторонней таблицы - он есть же в InvoicesItems
$Columns = Array('OrderID','Amount','(SELECT `ServiceID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) as `ServiceID`','(SELECT `Priority` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) AS `Priority`');
#-------------------------------------------------------------------------------
$Items = DB_Select('InvoicesItems',$Columns,Array('SortOn'=>Array('Priority','Summ'),'IsDesc'=>TRUE,'Where'=>SPrintF('`InvoiceID` = %u',$Invoice['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Items)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Items as $Item){
		#-------------------------------------------------------------------------------
		$Path = SPrintF('Services/%u',$Item['ServiceID']);
		#-------------------------------------------------------------------------------
		$Element = System_Element(SPrintF('comp/%s.comp.php',$Path));
		if(!Is_Error($Element)){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load($Path,$Item);
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$Number = Comp_Load('Formats/Order/Number',$Item['OrderID']);
				if(Is_Error($Number))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Event = Array(
						'UserID'	=> $Invoice['UserID'],
						'PriorityID'	=> 'Error',
						'Text'		=> SPrintF('Не удалось произвести автоматическую оплату заказа №%s, причина (%s)',$Number,$Comp->String),
						);
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'true':
				# No more...
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}elseif($Item['OrderID']){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/ServiceOrderPay',Array('ServiceOrderID'=>$Item['OrderID'],'AmountPay'=>$Item['Amount']));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$Number = Comp_Load('Formats/Order/Number',$Item['OrderID']);
				if(Is_Error($Number))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Event = Array(
						'UserID'	=> $Invoice['UserID'],
						'PriorityID'	=> 'Error',
						'Text'		=> SPrintF('Не удалось произвести автоматическую оплату заказа №%s, причина (%s)',$Number,$Comp->String)
						);
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				# No more...
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
$IsUpdate = DB_Update('Invoices',Array('IsPosted'=>TRUE),Array('ID'=>$Invoice['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
