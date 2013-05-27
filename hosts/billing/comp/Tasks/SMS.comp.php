<?php
#-------------------------------------------------------------------------------
/** @author Rootden for Lowhosting.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task', 'Mobile', 'Message', 'ID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Debug(SPrintF('[comp/Tasks/SMS]: отправка SMS сообщения для (%u)', $Mobile));
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = $Mobile;
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$FreeSMS = FALSE;
#-------------------------------------------------------------------------------
if (!Isset($Config['SMSGateway']['SMSProvider'])) {
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if (!Isset($Config['SMSGateway']['SMSKey'])) {
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if (!Isset($Config['SMSGateway']['SMSSender'])) {
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if (!Isset($Config['SMSGateway']['Exceptions']['SMSExceptionsPaidInvoices'])) {
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if (!Isset($Config['SMSGateway']['Exceptions']['SMSExceptionsSchemeID'])) {
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$SMSProvider = $Config['SMSGateway']['SMSProvider'];
$Key = $Config['SMSGateway']['SMSKey'];
$SMSsender = $Config['SMSGateway']['SMSSender'];
$LimitPaidInvoices = $Config['SMSGateway']['Exceptions']['SMSExceptionsPaidInvoices'];
$LimitSchemeID = $Config['SMSGateway']['Exceptions']['SMSExceptionsSchemeID'];
#-------------------------------------------------------------------------------
$User = DB_Select('Users', Array('MobileConfirmed', 'GroupID'), Array('UNIQ', 'ID' => $ID));
if (!Is_Array($User))
    return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// Не работаем если номер не подтвержден.
#-------------------------------------------------------------------------------
if ($User['MobileConfirmed'] == 0 && $Task != NULL) {
    return TRUE;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// Если пользователь относится к группе 'Сотрудники' то плату не взымаем...
#-------------------------------------------------------------------------------
if ($User['GroupID'] == '3000000') {
    $PaymentLock = true;
    $SMSCost = 0;
}
#-------------------------------------------------------------------------------
// Проверяем пользователя на исключения оплаты, сумма оплаченных счетов.
#-------------------------------------------------------------------------------
if ($LimitPaidInvoices != 100500) {
    $IsQuery = DB_Query(SPrintF('SELECT SUM( `Summ` ) FROM `InvoicesOwners`  WHERE `InvoicesOwners`.`UserID` = %u AND `InvoicesOwners`.`IsPosted` = \'yes\'', (integer) $ID));
    if (Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
    $Row = MySQL::Result($IsQuery);
    if (Is_Error($Row))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
    $SUMPaidInvoices = (integer) Current($Row[0]);
    if ($SUMPaidInvoices >= $LimitPaidInvoices) {
	$FreeSMS = true;
    }
    //Debug(SPrintF('[comp/Tasks/SMS]: Оплаченных счетов (%s)', $SUMPaidInvoices));
}
#-------------------------------------------------------------------------------
// Проверяем пользователя на исключения оплаты, активные заказы хостинга.
#-------------------------------------------------------------------------------
if ($LimitSchemeID != 0) {
    $OrderHostings = DB_Select('HostingOrdersOwners', 'SchemeID', Array('Where' => SPrintF('`UserID` = %u AND `StatusID` = \'Active\'', $ID)));
    if (Is_Error($OrderHostings))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
    $ArrLimitSchemeID = explode(',', $LimitSchemeID);
    foreach ($OrderHostings as $OrderHosting) {
	if (in_array((integer) $OrderHosting['SchemeID'], $ArrLimitSchemeID)) {
	    $FreeSMS = true;
	    break;
	}
    }
    //Debug(print_r($ArrLimitSchemeID, true));
}
#-------------------------------------------------------------------------------
$MessageLength = mb_strlen($Message);
Debug(SPrintF('[comp/Tasks/SMS]: Собщение (%s) Длинна (%s)', $Message, $MessageLength));
#-------------------------------------------------------------------------------
if (Is_Error(System_Load(SPrintF('classes/%s.class.php', $SMSProvider))))
    return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/SMS]: SMS шлюз (%s)', $SMSProvider));
Debug(SPrintF('[comp/Tasks/SMS]: API ключ (%s)', $Key));
Debug(SPrintF('[comp/Tasks/SMS]: Отправитель (%s)', $SMSsender));
#-------------------------------------------------------------------------------
if (!isset($PaymentLock)) {
    $Regulars = Regulars();
    $MobileCountry = 'SMSPriceDefault';
    $RegCountrys = array('SMSPriceRu' => $Regulars['SMSPriceRu'], 'SMSPriceUa' => $Regulars['SMSPriceUa'], 'SMSPriceSng' => $Regulars['SMSPriceSng'], 'SMSPriceZone1' => $Regulars['SMSPriceZone1'], 'SMSPriceZone2' => $Regulars['SMSPriceZone2']);
    #-------------------------------------------------------------------------------
    foreach ($RegCountrys as $RegCountryKey => $RegCountry) {
	if (Preg_Match($RegCountry, $Mobile)) {
	    $MobileCountry = $RegCountryKey;
	}
    }
    Debug(SPrintF('[comp/Tasks/SMS]: Страна определена (%s)', $MobileCountry));
    #-------------------------------------------------------------------------------
    if (!Isset($Config['SMSGateway']['Price'][$MobileCountry])) {
	return ERROR | @Trigger_Error(500);
    }
    #-------------------------------------------------------------------------------
    if ($MessageLength <= 70) {
	$SMSCost = Str_Replace(',', '.', $Config['SMSGateway']['Price'][$MobileCountry]);
	$SMSCount = 1;
    }
    else {
	$SMSCount = ceil($MessageLength / 67);
	$SMSCost = $SMSCount * Str_Replace(',', '.', $Config['SMSGateway']['Price'][$MobileCountry]);
    }
    #-------------------------------------------------------------------------------
    if ($FreeSMS === TRUE)
	$SMSCost = 0;
    Debug(SPrintF('[comp/Tasks/SMS]: Стоимость сообщения (%s) всего частей (%s)', $SMSCost, $SMSCount));
    #-------------------------------------------------------------------------------
    if (!is_numeric($SMSCost))
	return ERROR | @Trigger_Error(500);
    #-------------------------------------------------------------------------
    if ($SMSCost > 0) {
	$Contracts = DB_Select('Contracts', Array('TypeID', 'ID', 'Balance'), Array('Where' => SPrintF('`UserID` = %u', $ID)));
	if (!Is_Array($Contracts))
	    return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach ($Contracts as $Contract) {
	    if ($Contract['TypeID'] !== 'NaturalPartner' && $Contract['Balance'] >= $SMSCost) {
		$ContractID = $Contract['ID'];
		(integer) $After = $Contract['Balance'] - $SMSCost;
		break;
	    }
	}
	#-------------------------------------------------------------------------------
	if (!isset($ContractID) && !isset($After)) {
	    Debug("[comp/Tasks/SMS]: Недостаточно денежных средств на каком либо договоре клиента");
	    if ($Config['Notifies']['Methods']['SMS']['IsEvent']) {
		$Event = Array('UserID' => $ID, 'PriorityID' => 'Error', 'Text' => SPrintF('Не удалось отправить SMS сообщение для (%s), %s', $Mobile, 'недостаточно денежных средств.'));
		$Event = Comp_Load('Events/EventInsert', $Event);
		if (!$Event)
		    return ERROR | @Trigger_Error(500);
	    }
	    #-------------------------------------------------------------------------------
	    if ($Task === NULL)
		return "Недостаточно денежных средств на балансе. Стоимость: $SMSCost";
	    #-------------------------------------------------------------------------------
	    return TRUE;
	}
    }
}
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$LinkID = Md5($SMSProvider);
#-------------------------------------------------------------------------------
if (!IsSet($Links[$LinkID])) {
    #-----------------------------------------------------------------------------
    $Links[$LinkID] = NULL;
    #-----------------------------------------------------------------------------
    $SMS = &$Links[$LinkID];
    #-----------------------------------------------------------------------------
    $SMS = new $SMSProvider($Key, $SMSsender);
    if (Is_Error($SMS))
	return ERROR | @Trigger_Error(500);
    #-----------------------------------------------------------------------------
    $IsAuth = $SMS->balance();
    switch (ValueOf($IsAuth)) {
	case 'false':
	    #-------------------------------------------------------------------------
	    Debug("[comp/Tasks/SMS]: Подключаемся и получаем баланс -> Error:'".$SMS->error."'");
	    if ($Config['Notifies']['Methods']['SMS']['IsEvent']) {
		$Event = Array('UserID' => $ID, 'PriorityID' => 'Error', 'Text' => SPrintF('Не удалось отправить SMS сообщение для (%s), %s', $Mobile, 'шлюз временно недоступен.'));
		$Event = Comp_Load('Events/EventInsert', $Event);
		if (!$Event)
		    return ERROR | @Trigger_Error(500);
	    }
	    UnSet($Links[$LinkID]);
	    #-------------------------------------------------------------------------------
	    if ($Task === NULL)
		return "Пожалуйста, попробуйте повторить попытку позже";
	    #-------------------------------------------------------------------------
	    return TRUE;
	#-------------------------------------------------------------------------
	case 'true':
	    #-------------------------------------------------------------------------
	    Debug("[comp/Tasks/SMS]: Подключаемся и получаем баланс -> Баланс: '".$SMS->balance."'");
	    break;
	#-------------------------------------------------------------------------
	default:
	    return ERROR | @Trigger_Error(101);
    }
    //Проверим баланс и отложим задачу в случае нехватки кредитов
    #-------------------------------------------------------------------------
    $SMSBalanse = (integer) $SMS->balance;
    if ($SMSBalanse == 0 || $SMSBalanse < $SMSCost) {
	if ($Config['Notifies']['Methods']['SMS']['IsEvent']) {
	    $Event = Array('UserID' => $ID, 'PriorityID' => 'Error', 'Text' => SPrintF('Не удалось отправить SMS сообщение для (%s), %s', $Mobile, 'недостаточно средств на шлюзе.'));
	    $Event = Comp_Load('Events/EventInsert', $Event);
	    if (!$Event)
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------
	if ($Task === NULL)
	    return "Пожалуйста, попробуйте повторить попытку позже";
	#-------------------------------------------------------------------------
	UnSet($Links[$LinkID]);
	return 3600;
    }
    #-------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$SMS = &$Links[$LinkID];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsMessage = $SMS->send((integer) $Mobile, $Message, $SMSsender);
switch (ValueOf($IsMessage)) {
    case 'false':
	#-------------------------------------------------------------------------
	Debug("[comp/Tasks/SMS]: Неудачно, ошибка:'".$SMS->error."'");
	if ($Config['Notifies']['Methods']['SMS']['IsEvent']) {
	    $Event = Array('UserID' => $ID, 'Text' => SPrintF('Не удалось отправить SMS сообщение для (%s), %s', $Mobile, 'шлюз временно недоступен.'));
	    $Event = Comp_Load('Events/EventInsert', $Event);
	    if (!$Event)
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------
	if ($Task === NULL)
	    return "Пожалуйста, попробуйте повторить попытку позже";
	#-------------------------------------------------------------------------
	UnSet($Links[$LinkID]);
	return 300;
    #-------------------------------------------------------------------------
    case 'true':
	Debug("[comp/Tasks/SMS]: Удачно, ответ шлюза:'".$SMS->success."'");
	if (!isset($PaymentLock) && isset($After)) {
	    #------------------------------TRANSACTION--------------------------
	    if (Is_Error(DB_Transaction($TransactionID = UniqID('PostingSMS'))))
		return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
	    $IsUpdated = DB_Update('Contracts', Array('Balance' => $After), Array('ID' => $ContractID));
	    if (Is_Error($IsUpdated))
		return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
	    $IPosting = Array(
		#-----------------------------------------------------------------
		'ContractID' => $ContractID,
		'ServiceID' => '2000',
		'Comment' => "За смс уведомление ($SMSCount шт)",
		'Before' => $Contract['Balance'],
		'After' => $After
	    );
	    #-------------------------------------------------------------------
	    $PostingID = DB_Insert('Postings', $IPosting);
	    if (Is_Error($PostingID))
		return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
	    if (Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	    #-------------------------END TRANSACTION---------------------------
	    Debug(SPrintF('[comp/Tasks/SMS]: Договор id (%s) баланс до оплаты (%s) после оплаты (%s)', $ContractID, $Contract['Balance'], $After));
	}
	break;
    default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if (!$Config['Notifies']['Methods']['SMS']['IsEvent'])
    return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array('UserID' => $ID, 'Text' => SPrintF('SMS сообщение для (%s) успешно отправлено.', $Mobile));
#-------------------------------------------------------------------------------
$Event = Comp_Load('Events/EventInsert', $Event);
#-------------------------------------------------------------------------------
if (!$Event)
    return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
