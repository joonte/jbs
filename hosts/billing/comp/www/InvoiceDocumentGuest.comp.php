<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$Domain		= (string) @$Args['domain'];
$Hostname	= (string) @$Args['hostname'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/DOM.class.php','libs/Upload.php','classes/Net_IDNA.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IDNA = new Net_IDNA();
#-------------------------------------------------------------------------------
$IDNA->decode($Domain);
#-------------------------------------------------------------------------------
if($Domain)
	if($Domain != $IDNA->decode($Domain))
		$Domain = $IDNA->decode($Domain);
#-------------------------------------------------------------------------------
if($Hostname)
	if($Hostname != $IDNA->decode($Hostname))
		$Hostname = $IDNA->decode($Hostname);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['InvoiceDocumentGuest'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Domain != $Hostname)
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: %s != %s',$Domain,$Hostname));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($_COOKIE['SessionID']) && $Settings['RedirectAuthorized']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: у юзера есть сессия, авторизован, редиректим'));
	#-------------------------------------------------------------------------------
	Header('Location: /v2/HostingOrders');
	#-------------------------------------------------------------------------------
	return NULL;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddAttribs('MenuLeft',Array('args'=>'Site'));
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, что гостям вообще разрешена оплата заказов
if(!$Settings['IsActive']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: оплата от гостей отключена'));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P','Гостевая оплата запрещена.'));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Domain && !$Hostname){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: пустые переменные Domain и Hostname'));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P','Не задано доменное имя заказа хостинга'));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if(!$Domain)
	$Domain = $Hostname;
#-------------------------------------------------------------------------------
if(SubStr($Domain,0,4) == 'www.')
	$Domain = SubStr($Domain,4,StrLen($Domain));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Domain)){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: мусор в доменном имени'));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P','Неверно указано доменное имя. Вы прошли по ссылке или ручками балуетесь? =)'));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем переданное доменное имя в заказах хостинга, пока - безотносительно статуса
$Where = Array(SPrintF("`Domain` = '%s' OR `Parked` LIKE '%s,%%' OR `Parked` LIKE '%%,%s,%%' OR `Parked` LIKE '%%%s'",$Domain,$Domain,$Domain,$Domain));
#-------------------------------------------------------------------------------
$Count = DB_Count('HostingOrdersOwners',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: не найдено заказов с доменом %s',$Domain));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P',SPrintF('Заказов хостинга с доменом "%s" не найдено ни в каком статусе.',$Domain)));
	#-------------------------------------------------------------------------------
	# отчехлить субдомен у домена и проверить ещё раз
	$Parts = Explode('.',$Domain);
	#-------------------------------------------------------------------------------
	UnSet($Parts[0]);
	#-------------------------------------------------------------------------------
	$Domain = Implode('.',$Parts);
	#-------------------------------------------------------------------------------
	# снова ищщем переданное доменное имя в заказах хостинга
	$Where = Array(SPrintF("`Domain` = '%s' OR `Parked` LIKE '%s,%%' OR `Parked` LIKE '%%,%s,%%' OR `Parked` LIKE '%%%s'",$Domain,$Domain,$Domain,$Domain));
	#-------------------------------------------------------------------------------
	$Count = DB_Count('HostingOrdersOwners',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count){
		#-------------------------------------------------------------------------------
		$DOM->AddText('Title',SPrintF('Оплата заказа хостинга, домен "%s"',$Domain));
		$NoBody->AddChild(new Tag('P',SPrintF('Оплата невозможна - заказов хостинга с доменом "%s" не найдено.',$Domain)));
		$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Into',$NoBody);
		#-------------------------------------------------------------------------------
		$Out = $DOM->Build();
		#-------------------------------------------------------------------------------
		if(Is_Error($Out))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($Settings['SendReportOnSearchError'])
			Report(SprintF('Не найден домен %s при попытке выписать счёт',$Domain));
		#-------------------------------------------------------------------------------
		return $Out;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Оплата заказа хостинга, домен "%s"',$Domain));
$NoBody->AddChild(new Tag('P',SPrintF('Найден заказ хостинга с доменом "%s".',$Domain)));
#-------------------------------------------------------------------------------
# ищщем заказы хостинга с заблокированным доменом
$Where[] = '`StatusID` = "Suspended"';
#-------------------------------------------------------------------------------
$Count = DB_Count('HostingOrdersOwners',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P',SPrintF('Оплата невозможна - заказ хостинга с доменом "%s" не заблокирован.',$Domain)));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($Count > 1){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: найдено %s заказов с доменом %s',$Count,$Domain));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P',SPrintF('Оплата невозможна - с доменом "%s" найдено более одного заблокированного заказа.',$Domain)));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему и оплатите от своего имени.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','OrderID','UserID','ContractID',
		'(SELECT `IsProlong` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) AS `IsProlong`',
		'(SELECT `MinDaysProlong` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) AS `MinDaysProlong`'
		);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
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
# проверяем что у тарифа есть возможность продления. 
if(!$HostingOrder['IsProlong']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: тариф не позволяет продление'));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('P','Оплата невозможна - заказ хостинга не позволяет продление.'));
	$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему, смените тарифный план и продлите заказ.'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// првоеряем, подтверждён ли аккаунт
$NeedConfirmed = $Config['Interface']['User']['InvoiceMake']['NeedConfirmed'];
#-------------------------------------------------------------------------------
if($NeedConfirmed != "NONE"){
	#-------------------------------------------------------------------------------
	// достаём юзера
	$Owner = DB_Select('Users',Array('ConfirmedWas'),Array('UNIQ','ID'=>$HostingOrder['UserID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Owner)){
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
	if(!SizeOf($Owner['ConfirmedWas'])){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: пользователь не подтверждён'));
		#-------------------------------------------------------------------------------
		$NoBody->AddChild(new Tag('P','Оплата невозможна - аккаунт клиента не подтверждён.'));
		$NoBody->AddChild(new Tag('P','Если вы владелец этого сайта, войдите в биллинговую систему, добавьте и подтвердите телефон, после чего будет возможно продлить заказ'));
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Into',$NoBody);
		#-------------------------------------------------------------------------------
		$Out = $DOM->Build();
		#-------------------------------------------------------------------------------
		if(Is_Error($Out))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return $Out;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем неоплаченный счёт на определённую платёжную систему,
# которой можно оплачивать такие счета (выбора не будет, т.к. тут будет сильно всё обрезанное),
# на число дней в настройках, только на оплату этого заказа. 
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
# если не задана, или задана несуществующая - выбираем первую
if(!$Settings['ForcePaymentSystem'] || !In_Array($Settings['ForcePaymentSystem'],$Array))
	$Settings['ForcePaymentSystem'] = $Array[0];
#-------------------------------------------------------------------------------
$PaymentSystemID = $Settings['ForcePaymentSystem'];
#-------------------------------------------------------------------------------
# чекаем число дней продления
$Settings['DaysPay'] = IntVal($Settings['DaysPay']);
#-------------------------------------------------------------------------------
if($Settings['DaysPay'] < $HostingOrder['MinDaysProlong'])
	$Settings['DaysPay'] = $HostingOrder['MinDaysProlong'];
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/InvoiceDocumentGuest]: PaymentSystemID = %s; DaysPay = %s;',$PaymentSystemID,$Settings['DaysPay']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# инициализируем системного юзера
$Comp = Comp_Load('Users/Init',100);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		SPrintF('`PaymentSystemID` = "%s"',$PaymentSystemID),	/* платёжная система совпадает */
		SPrintF('`ContractID` = %u',$HostingOrder['ContractID']),		/* договор совпадает */
		SPrintF('(SELECT SUM(`Amount`) FROM `InvoicesItems` WHERE `InvoiceID` = `Invoices`.`ID`) = %u',$Settings['DaysPay']),	/* число дней */
		'(SELECT COUNT(*) FROM `InvoicesItems` WHERE `InvoiceID` = `Invoices`.`ID`) = 1',	/* в составе счёта только один оъект */
		'`StatusID` = "Waiting"'						/* не оплачен */
		);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','Where'=>$Where,'Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	Debug('[comp/www/InvoiceDocumentGuest]: счёт не найден, необходимо выписать');
	#-------------------------------------------------------------------------------
	# Чистим юзеру корзину
	$iBasket = DB_Select('BasketOwners','ID',Array('Where'=>SPrintF('`UserID` = %u',$HostingOrder['UserID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($iBasket)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach($iBasket as $Basket)
			$Array[] = $Basket['ID'];
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('Basket',Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Array))));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	# выписываем счёт
	$Comp = Comp_Load('www/API/HostingOrderPay',Array('HostingOrderID'=>$HostingOrder['ID'],'DaysPay'=>$Settings['DaysPay'],'IsUseBasket'=>TRUE));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/InvoiceMake',Array('ContractID'=>$HostingOrder['ContractID'],'PaymentSystemID'=>$PaymentSystemID,'PayMessage'=>'Выставление счёта с гостевого аккаунта'));
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
	$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Comp['InvoiceID']));
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
	break;
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# ампутируем системного юзера
UnSet($GLOBALS['__USER']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выводим окно оплаты, со счётом, со всеми обрубленными данными - типа имя юзера, мыл, и т.п... как вариант - предложить это ввести оплачивающему. 
// проверяем наличие файла
$Files = GetUploadedFiles('Invoices',$Invoice['ID']);
#-------------------------------------------------------------------------------
if(SizeOf($Files)){
	#-------------------------------------------------------------------------------
	// файл есть, используем последний элемент массива
	$File = End($Files);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	return ERROR | @Trigger_Error('Счёт не сформирован');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Document = new DOM($File['Data']);
#-------------------------------------------------------------------------------
$Document->Delete('Logo');
#-------------------------------------------------------------------------------
$Document->Delete('Rubbish');
#-------------------------------------------------------------------------------
$Document->DeleteIDs();
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('class'=>'Standard','style'=>'max-width:700px;'),$Document->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# инициализируем гостя
$Comp = Comp_Load('Users/Init',10);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsOwners',Array('Where'=>'`UserID` = 10'));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractMake',Array('TypeID'=>'Default'));
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
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$PaymentSystem = $Config['Invoices']['PaymentSystems'][$PaymentSystemID];
#-------------------------------------------------------------------------------
if($PaymentSystem['IsContinuePaying']){
	#-------------------------------------------------------------------------------
	# инициализируем гостя
	$Comp = Comp_Load('Users/Init',10);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('onclick'=>"ShowProgress('Вход в платежную систему');form.submit();",'type'=>'button','style'=>'font-size:25px;color:#F07D00;','value'=>'Оплатить →'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# если тестовый режим - то другой URL
	$Cpp = (IsSet($PaymentSystem['TestMode']) && $PaymentSystem['TestMode'] && IsSet($PaymentSystem['TestModeCpp']))?$PaymentSystem['TestModeCpp']:$PaymentSystem['Cpp'];
	#-------------------------------------------------------------------------------
	$Form = new Tag('FORM',Array('action'=>$Cpp,'method'=>'POST'),new Tag('BR'),new Tag('DIV',$Comp));
	#-------------------------------------------------------------------------------
	$Send = Comp_Load(SPrintF('Invoices/PaymentSystems/%s',$PaymentSystem['Comp']),$PaymentSystemID,$Invoice['ID'],$Invoice['Summ']);
	if(Is_Error($Send))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Send) as $ParamID){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>$ParamID,'type'=>'hidden','value'=>$Send[$ParamID]));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Div->AddChild($Form);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$NoBody->AddChild($Div);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
UnSet($GLOBALS['__USER']);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$NoBody);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
