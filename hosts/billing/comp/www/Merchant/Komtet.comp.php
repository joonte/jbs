<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
Debug(SPrintF("[comp/www/Merchant/Komtet]: Args = %s",print_r($Args,true)));


# Получить JSON как строку
$json_str = file_get_contents('php://input');
# Получить объект
$json_obj = json_decode($json_str);
Debug(SPrintF("[comp/www/Merchant/Komtet]: json_obj = %s",print_r($json_obj,true)));
$Headers = Apache_Request_Headers();
Debug(SPrintF("[comp/www/Merchant/Komtet]: X-HMAC-Signature = %s",@$Headers['X-HMAC-Signature']));

#$headers = apache_request_headers();

#foreach ($headers as $header => $value) {
#	Debug(SPrintF("[comp/www/Merchant/Komtet]: %s: %s",$header,$value));
#}













return TRUE;







if(!Count($Args))
	return "No args...\n";
#-------------------------------------------------------------------------------
$ArgsIDs = Array('amount','checksum','mdOrder','operation','orderNumber','status');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
	$Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
UkSort($Args, "strcasecmp");
#-------------------------------------------------------------------------------
#Debug(SPrintF("[comp/www/Merchant/Komtet]: Args = %s",print_r($Args,true)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# файл в котором хранятся номера счетов и адреса редиректа
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
	return ERROR | @Trigger_Error('[Komtet_Get_Tmp]: не удалось найти временную папку');
#-------------------------------------------------------------------------------
$KomtetFileDB = SPrintF('%s/Komtet.txt',$Tmp);
#-------------------------------------------------------------------------------
if(!File_Exists($KomtetFileDB)){
	#-------------------------------------------------------------------------------
	$IsWrite = IO_Write($KomtetFileDB,"#Invoice\tURL\n");
	if(Is_Error($IsWrite))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderID = $Args['orderNumber'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Komtet'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','Summ','ContractID'),Array('UNIQ','ID'=>$OrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
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
$InvoiceID = $Invoice['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# два варианта - задан или нет redirect
if(IsSet($Args['redirect'])){
	#-------------------------------------------------------------------------------
	# возможный вариант - повторная попытка оплаты. достаём данные по этому счёту
	$IsRead = IO_Read($KomtetFileDB);
	if(Is_Error($IsRead))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Lines = Explode("\n", Trim($IsRead));
	#-------------------------------------------------------------------------------
	foreach($Lines as $Line){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[comp/www/Merchant/Komtet]: Line = %s",$Line));
		#-------------------------------------------------------------------------------
		List($InvoiceTMP, $URL) = Preg_Split("/[\s]+/",$Line);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[comp/www/Merchant/Komtet]: InvoiceTMP = %s; URL = %s",$InvoiceTMP,$URL));
		#-------------------------------------------------------------------------------
		if($InvoiceTMP == $InvoiceID){
			#-------------------------------------------------------------------------------
			Header(SPrintF('Location: %s',$URL));
			#-------------------------------------------------------------------------------
			exit;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# надо запросить в сбербанке URL на который отсылаем клиента и выдать заголовок с отправкой на этот URL
	#-------------------------------------------------------------------------------
	# библиотека для работы с ХТТП
	if(Is_Error(System_Load('libs/HTTP.php')))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$HTTP = Array(
			'Address'	=> (($Settings['TestMode'])?'3dsec.sberbank.ru':'securepayments.sberbank.ru'),
			'Port'		=> 443,
			'Host'		=> (($Settings['TestMode'])?'3dsec.sberbank.ru':'securepayments.sberbank.ru'),
			'Protocol'	=> 'ssl',
			'Charset'	=> 'UTF-8',
			'Hidden'	=> $Settings['Password'],
			'IsLogging'	=> FALSE
			);
	#-------------------------------------------------------------------------------
	# дата окончания действия счёта. время до отмены + время до удаления счёта
	$expirationDate = ($Config['Tasks']['Types']['GC']['Invoices']['DaysBeforeDeleted'] + $Config['Tasks']['Types']['GC']['Invoices']['DaysBeforeErase'] + 1) * 24 * 60 * 60 + Time();
	#-------------------------------------------------------------------------------
	$Query = Array(
			'userName'	=> $Settings['Login'],
			'password'	=> $Settings['Password'],
			'orderNumber'	=> $InvoiceID,
			'amount'	=> $Invoice['Summ'] * 100,
			'currency'	=> $Args['currency'],
			'returnUrl'	=> $Args['returnUrl'],
			'failUrl'	=> $Args['failUrl'],
			'description'	=> $Args['description'],
			'expirationDate'=> Date('c',$expirationDate)
			);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Result = HTTP_Send('/payment/rest/register.do',$HTTP,Array(),$Query);
	if(Is_Error($Result))
		return ERROR | @Trigger_Error('[Komtet_Get_Invoice_URL]: не удалось выполнить запрос к серверу');
	#-------------------------------------------------------------------------------
	$Result = Trim($Result['Body']);
	#-------------------------------------------------------------------------------
	$Result = Json_Decode($Result,TRUE);
	#-------------------------------------------------------------------------------
	#Debug(print_r($Result,true));
	#-------------------------------------------------------------------------------
	if(IsSet($Result['formUrl'])){
		#-------------------------------------------------------------------------------
		# сохраняем переданный URL
		$IsWrite = IO_Write($KomtetFileDB,SPrintF("%s\t\t%s\n",$InvoiceID,$Result['formUrl']));
		if(Is_Error($IsWrite))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		Header(SPrintF('Location: %s',$Result['formUrl']));
		#-------------------------------------------------------------------------------
		exit;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		# ошибка, нет УРЛ
		return ERROR | @Trigger_Error('[comp/www/Merchant/Komtet]: URL не передан, какая-то ошибка');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Args['status']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF("[comp/www/Merchant/Komtet]: операция неуспешна, status = %s",$Args['status']));
	#-------------------------------------------------------------------------------
	return "Not success operation, ignored...";
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Values = '';
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
	if($ArgID != 'checksum')
		if($Args[$ArgID])
			$Values .= SPrintF('%s;%s;',$ArgID,$Args[$ArgID]);
#-------------------------------------------------------------------------------
#Debug(SPrintF("[comp/www/Merchant/Komtet]: Values = %s",$Values));
#-------------------------------------------------------------------------------
if($Args['checksum'] != StrToUpper(Hash_Hmac('sha256',$Values,$Settings['Hash'])))
	return ERROR | @Trigger_Error('[comp/www/Merchant/Komtet]: проверка подлинности завершилась неудачей');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если сумма не задана, или равна нулю
if(!IsSet($Args['amount']) || !$Args['amount']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF("[comp/www/Merchant/Komtet]: параметр 'amount' не передан, проверка суммы платежа невозможна"));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($Invoice['Summ'] != $Args['amount'] / 100)
		return ERROR | @Trigger_Error(SPrintF('[comp/www/Merchant/Komtet]: сумма платежа не совпадает, %s != %s',$Invoice['Summ'],($Args['amount'] / 100)));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Init',100);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
switch($Args['operation']){
case 'approved':
	#-------------------------------------------------------------------------------
	$StatusID = 'Waiting';
	#-------------------------------------------------------------------------------
	$Comment = 'Средства успешно заблокированы (выполнена авторизационная транзакция)';
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'deposited':
	#-------------------------------------------------------------------------------
	$StatusID = 'Payed';
	#-------------------------------------------------------------------------------
	$Comment = 'Оплачен (выполнена финансовая транзакция или заказ оплачен в электронной платёжной системе)';
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'refunded':
	#-------------------------------------------------------------------------------
case 'reversed':
	#-------------------------------------------------------------------------------
	# если сумма совпадает - отменяем транзакцию
	if(!IsSet($Args['amount']) || $Args['amount'] != $Invoice['Summ'] * 100){
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[comp/www/Merchant/Komtet]: переданная сумма (%s) не совпадает с суммой счёта (%s)",$Args['amount']/100,$Invoice['Summ']));
		#-------------------------------------------------------------------------------
		return "Summ does not match";
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$StatusID = 'Rejected';
	#-------------------------------------------------------------------------------
	$Comment = 'Отменён (выполнена транзакция разблокировки	средств или выполнена операция по возврату платежа после списания средств)';
	#-------------------------------------------------------------------------------
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('Merchant/Komtet'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# ставим счёт как неоплаченный
	# плохая идея. появляется кнопка про оплату, а оплатить нельзя - юнителлер не даёт
	#$IsUpdate = DB_Update('Invoices',Array('IsPosted'=>FALSE),Array('ID'=>$Invoice['ID']));
	#if(Is_Error($IsUpdate))
	#	return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# вычитаем сумму счёта из договора, на который счёт.
	$Contract = DB_Select('ContractsOwners','Balance',Array('UNIQ','ID'=>$Invoice['ContractID']));
	switch(ValueOf($Contract)){
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
	# UPDATE 2018-04-30 in 20:32 MSK
	# ничего не делаем. эти придурки независимо от суммы возврата присылают сумму которая была оплачена
	#$After = $Contract['Balance'] - $Invoice['Summ'];
	#-------------------------------------------------------------------------------
	#$IsUpdate = DB_Update('Contracts',Array('Balance'=>$After),Array('ID'=>$Invoice['ContractID']));
	#if(Is_Error($IsUpdate))
	#	return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# заносим запись в историю операций с контрактами
	$Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
	if(Is_Error($Number))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#$IPosting = Array(
	#		'ContractID' => $Invoice['ContractID'],
	#		'ServiceID'  => 2000,
	#		'Comment'    => SPrintF('Возврат средств зачисленных по счёту #%u (транзакция отменена)',$Number),
	#		'Before'     => $Contract['Balance'],
	#		'After'      => $After
	#		);
	#-------------------------------------------------------------------------------
	#$PostingID = DB_Insert('Postings',$IPosting);
	#if(Is_Error($PostingID))
	#	return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Event = Array('UserID'=>$Invoice['UserID'],'PriorityID'=>'Billing','IsReaded'=>FALSE,'Text'=>SPrintF('Произведён возврат средств по счёту #%u, процессинговый центр прислал статус "%s". Сумму сбербанк не присылает, необходимо списывать самостоятельно.',$Number,$Args['operation']));
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Invoices/PaymentSystems/Komtet]: статус "%s", счёт #%u проигнорирован',$Args['operation'],$InvoiceID));
	return "OK\n";
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>$StatusID,'RowsIDs'=>$InvoiceID,'Comment'=>$Comment));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
# если была транзакция - коммитим
if(IsSet($TransactionID))
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return "OK\n";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
